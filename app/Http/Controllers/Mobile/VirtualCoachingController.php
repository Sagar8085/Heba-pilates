<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\CartBooking;
use App\Models\MemberPackage;
use App\Models\Order;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VirtualCoachingController extends Controller
{
    public function trainers(): JsonResponse
    {
        $users = User::onlyTrainers()->with('trainer')->get();

        return response()->json($users);
    }

    public function slots(User $user, Request $request): JsonResponse
    {
        $timeslots = collect();

        if ($request->date === 'today') {
            $request->merge([
                'date' => Carbon::now()->format('Y-m-d'),
            ]);
        }

        if ($request->date === 'tomorrow') {
            $request->merge([
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            ]);
        }

        /**
         * Load all sessions on this day for this trainer,
         * as later when we build the slots on we will check
         * if a slot has already been taken.
         */
        $sessions = Session::where('date', $request->date)->where('trainer_id',
            $user->id)->get()->pluck('start')->toArray();

        /**
         * Load the available for the trainer on the specified day.
         */
        $availability = Availability::where('user_id', $user->id)
            ->where('date', $request->date)
            ->orderBy('start', 'ASC')
            ->get();


        foreach ($availability as $row) {

            $availabilityStart = Carbon::parse($row->start);
            $availabilityEnd = Carbon::parse($row->end);

            /**
             * Loop through 1 hour intervals until we reach the end time of the availability.
             */
            while ($availabilityStart < $availabilityEnd) {

                /**
                 * If it's today, don't show slots that have passed.
                 */
                $timeslotHour = $availabilityStart->format('H');
                if ($request->date == date('Y-m-d') && $timeslotHour <= date('H')) {
                    $availabilityStart->addMinutes(60);
                    continue;
                }

                /**
                 * If there is no session already booked for this time, add to the available timeslots array.
                 */
                $timeCheck = $availabilityStart->format('H:i:s');

                if (!in_array($timeCheck, $sessions)) {
                    $timeslots->push([
                        'time' => $availabilityStart->format('H:i:s'),
                        'time_formatted' => $availabilityStart->format('g:ia'),
                        'date' => $request->date,
                        'datetime' => $request->date . ' ' . $availabilityStart->format('H:i:s'),
                        'date_formatted' => $availabilityStart->format('l dS F'),
                        'trainer' => User::where('id', $user->id)->with('trainer')->first(),
                    ]);
                }

                $availabilityStart->addMinutes(60);
            }
        }

        return response()->json($timeslots);
    }

    public function booking(Request $request): JsonResponse
    {
        $this->validate($request, [
            'bookings' => 'required',
            'trainer_id' => 'required',
            'member_package_id' => 'required',
        ]);

        $failedBookings = collect();

        /**
         * Loop through each of the bookings and check they are still available.
         */
        foreach ($request->bookings as $slot) {
            $bookingCheck = Session::where('trainer_id', $request->trainer_id)
                ->where('datetime', $slot['date'] . ' ' . $slot['time'])
                ->first();

            if ($bookingCheck !== null) {
                $slot['available'] = false;
                $failedBookings->push($slot);
            }
        }

        if ($failedBookings->count() > 0) {
            return response()->json([
                'status' => 'failure',
                'failed_bookings' => $failedBookings,
            ]);
        }

        /**
         * If we get here, no bookings failed, let's save them.
         */
        foreach ($request->bookings as $slot) {
            $memberPackage = MemberPackage::where('id', $request->member_package_id)->first();

            Session::create([
                'member_id' => auth()->user()->id,
                'trainer_id' => $request->trainer_id,
                'datetime' => $slot['date'] . ' ' . $slot['time'],
                'date' => $slot['date'],
                'start' => $slot['time'],
                'length' => $memberPackage->length,
                'user_package_id' => $request->member_package_id,
            ]);

            $memberPackage->update([
                'remaining' => ($memberPackage->remaining - 1),
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }


    public function bookings(): JsonResponse
    {
        $sessions = Session::where('member_id', auth()->user()->id)
            ->with('trainer.trainer')
            ->orderBy('datetime', 'DESC')
            ->get();

        $packages = MemberPackage::where('member_id', auth()->user()->id)->get();

        $bookings = collect();

        foreach ($sessions as $session) {
            $bookings->push([
                'id' => $session->id,
                'type' => 'session',
                'status' => $session->status,
                'time_human' => $session->time_human,
                'start' => $session->start,
                'end' => $session->end,
                'length_human' => $session->length_human,
                'link' => 'https://google.com',
                'trainer' => $session->trainer,
                'profile' => $session->trainer->profile,
                'packageInfo' => $session->package,
            ]);
        }

        foreach ($packages as $package) {
            $bookings->push([
                'id' => $package->id,
                'type' => 'package',
                'active' => true,
                'package' => [],
                'trainer' => $package->trainer,
                'profile' => $package->trainer->profile,
                'credits' => $package->credits,
                'remaining' => $package->remaining,
                'session_length' => $package->length,
                'link' => '',
                'package' => $package->package,
                'datetime' => $package->created_at,
            ]);
        }

        $col = 'datetime';

        $sorted = $bookings->sortByDesc('datetime')->values()->all();

        return response()->json($sorted);
    }

    public function trainerProfile(User $user): JsonResponse
    {
        $user->trainer = $user->trainer;
        $user->packages = $user->packages;

        $member = User::loadFromBearer();

        if ($member !== null) {
            $myPackages = MemberPackage::where('member_id', $member->id)
                ->where('trainer_id', $user->id)
                ->where('remaining', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('expires')
                        ->orWhere('expires', '>', Carbon::now());
                })
                ->with('sessions')
                ->get();
        } else {
            $myPackages = [];
        }

        return response()->json([
            'profile' => $user,
            'my_packages' => $myPackages,
        ]);
    }

    public function availabilityCalendar(User $user, Request $request): JsonResponse
    {
        $weekStart = Carbon::parse($request->week_start)->format('Y-m-d');
        $weekEnd = Carbon::parse($request->week_end)->format('Y-m-d');

        /**
         * Fetch all the trainers availability slots for a week.
         */
        $allAvailability = Availability::where('user_id', $user->id)
            ->where('start', '>=', $weekStart . ' 00:00:00')
            ->where('end', '<=', $weekEnd . ' 23:59:59')
            ->get();

        /**
         * Only fetch the sessions this member has booked.
         */
        $member = User::loadFromBearer();

        if ($member !== null) {
            $mySessions = Session::where('member_id', $member->id)
                ->where('datetime', '>=', $weekStart . ' 00:00:00')
                ->where('datetime', '<=', $weekEnd . ' 23:59:59')
                ->get();
        } else {
            $mySessions = [];
        }

        return response()->json([
            'my_sessions' => $mySessions,
            // 'booked_sessions' => $bookedSessions
            'all_availability' => $allAvailability,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
        ]);
    }

    public function singleSession(Session $session): JsonResponse
    {
        $session->trainer->profile = $session->trainer->profile;
        $session->package = $session->package;
        $session->link = $session->link;

        return response()->json($session);
    }

    public function cancelSession(Session $session): JsonResponse
    {
        $session->delete();
        return response()->json(['status' => 'cancelled']);
    }

    public function newCart(Request $request): JsonResponse
    {
        $cart = CartBooking::create([
            'member_id' => auth()->user()->id,
            'trainer_id' => $request->trainer_id,
            'package_id' => $request->package_id,
            'bookings' => json_encode($request->bookings),
        ]);

        return response()->json($cart);
    }

    public function purchase(CartBooking $cart): JsonResponse
    {
        $failedBookings = collect();

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => 1999,
            'method' => 'stripe',
            'orderable_id' => $cart->package->id,
            'orderable_type' => 'App\Models\Package',
        ]);

        var_dump($cart);
        die();

        $memberPackage = MemberPackage::create([
            'member_id' => auth()->user()->id,
            'trainer_id' => $cart->trainer_id,
            'package_id' => $cart->package_id,
            'total' => $cart->package->credits,
            'remaining' => $cart->package->credits,
            'length' => $cart->package->session_length,
        ]);

        /**
         * Book the sessions if they have scheduled any with the booking.
         */
        $bookings = $cart->bookings;

        foreach ($bookings as $booking) {
            /**
             * Has this session already been booked for this trainer.
             */
            $bookingCheck = Session::where('datetime', $booking->datetime)->where('trainer_id',
                $cart->trainer_id)->first();

            if ($bookingCheck === null) {
                $session = Session::create([
                    'member_id' => auth()->user()->id,
                    'trainer_id' => $cart->trainer_id,
                    'datetime' => $booking->date . ' ' . $booking->time,
                    'date' => $booking->date,
                    'start' => $booking->time,
                    'length' => $cart->package->session_length,
                    'user_package_id' => $memberPackage->id,
                ]);

                $memberPackage->deductCredit();
            } else {
                $failedBookings->push($booking);
            }
        }

        /**
         * Once finished delete the cart as it's no longer needed.
         */
        $cart->delete();

        return response()->json([
            'status' => 'success',
            'failed_bookings' => $failedBookings,
        ]);
    }
}
