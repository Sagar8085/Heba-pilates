<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\CreditPackPurchase;
use App\Models\Gym;
use App\Models\OpeningTime;
use App\Models\Reformer;
use App\Models\ReformerBooking;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index()
    {
        return response()->json(
            Gym::query()
                ->orderBy('name', 'ASC')
                ->visible()
                ->get()
        );
    }

    public function single(Gym $gym)
    {
        return response()->json($gym);
    }

    /**
     * Get Custom Opening Times
     * @param \App\Models\Gym $gym
     * @return \Illuminate\Http\JsonResponse
     */
    public function openingCustom(Gym $gym)
    {
        $openingHours = OpeningTime::query()
            ->where('gym_id', '=', $gym->id)
            ->where('date', '>=', now())
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'opening' => $openingHours,
        ]);

    }

    public function reservationDates()
    {
        $dates = collect();

        $startDate = now();
        $promoPack = CreditPackPurchase::where('credit_pack_id', 11)->where('user_id',
            auth()->user()->id)->where('expires', '>', now())->first();

        /**
         * Users with a subscription can book until 1 week after their expiry date in advance.
         * Special Request - Users with the Unlimited Promo can book 14 days from their purchase date in advance. (If a user also has a subscription, the subscription will override this)
         * Users with a credit pack can only book 1 week in advance.
         */
        if (auth()->user()->subscription) {
            $endDate = Carbon::parse(auth()->user()->subscription->expires)->addWeeks(1)->format('Y-m-d');
        } else {
            if ($promoPack !== null) {
                $endDate = Carbon::parse($promoPack->expires)->format('Y-m-d');
            } else {
                $endDate = now()->addWeeks(1)->format('Y-m-d');
            }
        }

        while ($startDate->format('Y-m-d') <= $endDate) {
            $dates->push([
                'raw' => $startDate->format('Y-m-d'),
                'formatted' => $startDate->format('l dS F Y'),
            ]);
            $startDate->addDays(1);
        }

        return response()->json($dates);
    }

    public function reservationTimeslots(Gym $gym)
    {
        $date = request('date');

        $timeslots = collect();

        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        $customOpeningTime = OpeningTime::where('date', $date)->where('gym_id', $gym->id)->first();

        if ($customOpeningTime !== null) {

            if ($customOpeningTime->closed) {
                return response()->json([]);
            }

            $openingTime = Carbon::parse($date . ' ' . $customOpeningTime->opening_time);
            $closingTime = Carbon::parse($date . ' ' . $customOpeningTime->closing_time);

        } else {

            $openingTime = Carbon::parse($date . ' ' . $gym->operating_hours->$dayOfWeek[0]);
            $closingTime = Carbon::parse($date . ' ' . $gym->operating_hours->$dayOfWeek[1]);

        }

        $breakTimes = ($gym->trainer_break_times ?? [
                'monday' => [],
                'tuesday' => [],
                'wednesday' => [],
                'thursday' => [],
                'friday' => [],
                'saturday' => [],
                'sunday' => [],
            ])[$dayOfWeek];

        while ($openingTime < $closingTime) {

            if ($date === date('Y-m-d')) {
                if ($openingTime <= date('Y-m-d H:i:s')) {
                    $openingTime->addMinutes(30);
                    continue;
                }
            }

            if (
                in_array($openingTime->format('H:i:s'), $breakTimes, true)
                || in_array($openingTime->clone()->addMinutes(30)->format('H:i:s'), $breakTimes, true)
            ) {
                $openingTime->addMinutes(30);
                continue;
            }

            // Get the number of booked machines in this studio at this time.
            $bookedMachines = ReformerBooking::select('reformer_bookings.*', 'reformers.gym_id')
                ->where('datetime', $openingTime->format('Y-m-d H:i:s'))
                ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
                ->where('reformers.gym_id', $gym->id)
                ->get()
                ->count();

            // Get the total number of working machines.
            $totalMachines = Reformer::where('gym_id', $gym->id)
                ->where('status', 'working')
                ->count();

            // We will then subtract the number of bookedMachines from the totalMachines, if it's not zero then we still have machines available for booking.
            if (($totalMachines - $bookedMachines) === 0) {
                $openingTime->addMinutes(60);
                continue;
            }

            /**
             * Create a lovely Carbon date.
             */
            $slotFormatted = $openingTime->format('g:ia l dS F');
            $timeHuman = $openingTime->format('g:ia');
            $dateHuman = $openingTime->format('l dS F');
            $slotDate = $openingTime->format('Y-m-d');
            $slotTime = $openingTime->format('H:i:s');
            $slotDatetime = $openingTime->format('Y-m-d H:i:s');
            $nextTime = Carbon::parse($openingTime)->addMinutes(30)->format('Y-m-d H:i:s');

            $nextBookedMachines = ReformerBooking::select('reformer_bookings.*', 'reformers.gym_id')
                ->where('datetime', $nextTime)
                ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
                ->where('reformers.gym_id', $gym->id)
                ->get()
                ->count();

            if (($totalMachines - $nextBookedMachines) === 0) {
                $openingTime->addMinutes(30);
                continue;
            }

            $timeslots->push([
                'formatted' => $slotFormatted,
                'date' => $slotDate,
                'date_human' => $dateHuman,
                'time' => $slotTime,
                'time_human' => $timeHuman,
                'datetime' => $slotDatetime,
                'duration' => 60,
            ]);

            $openingTime->addMinutes(30);
        }

        $timeslots->pop();

        return response()->json($timeslots);
    }

    public function createReservation(Gym $gym, Request $request)
    {
        $min = Carbon::parse($request->datetime)->subMinutes(30)->format('Y-m-d H:i:s');
        $max = Carbon::parse($request->datetime)->addMinutes(30)->format('Y-m-d H:i:s');

        /**
         * Get a list of machines for this studio which are already booked at this time.
         */
        $bookedMachines = ReformerBooking::select('reformer_bookings.*', 'reformers.gym_id')
            ->where('datetime', '>=', $min)
            ->where('datetime', '<=', $max)
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->where('reformers.gym_id', $gym->id)
            ->get()
            ->pluck('reformer_id')
            ->toArray();

        $availableMachine = Reformer::whereNotIn('id', $bookedMachines)
            ->where('gym_id', $gym->id)
            ->where('status', 'working')
            ->first();

        if ($availableMachine === null) {
            // There is no machine available at this time.
            return response()->json([
                'status' => 'failure',
                'message' => 'We are very sorry, all of our Hebe Pilates machines are all booked up for this time period.',
            ]);
        }

        $reservation = ReformerBooking::create([
            'user_id' => auth()->user()->id,
            'reformer_id' => $availableMachine->id,
            'datetime' => $request->datetime,
        ]);

        $reservation->sendBookingConfirmationEmail();

        if ($request->paymentType === 'membership') {
            // Deduct a studio credit from this members subscription.
            $sub = auth()->user()->subscription;
            $sub->update([
                'studio_credits' => ($sub->studio_credits - 1),
            ]);

            $reservation->update([
                'bookable_type' => 'App\Models\Subscription',
                'bookable_id' => $sub->id,
            ]);
        } else {
            if ($request->paymentType === 'credit') {
                // Deduct a studio credit from this members credit pack.
                $creditPackPurchase = CreditPackPurchase::find($request->creditPackPurchaseId);
                $creditPackPurchase->update([
                    'studio_credits' => ($creditPackPurchase->studio_credits - 1),
                ]);

                $reservation->update([
                    'bookable_type' => 'App\Models\CreditPackPurchase',
                    'bookable_id' => $creditPackPurchase->id,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation,
        ]);
    }

    public function cancelReservation(ReformerBooking $reservation)
    {
        if ($reservation->user_id === auth()->user()->id) {
            \Log::debug($reservation->id . ': To Be Deleted');

            if ($reservation->bookable_type) {
                if ($reservation->datetime > date('Y-m-d H:i:s')) {
                    \Log::debug($reservation->id . ': Can Be Refunded');
                    if ($reservation->bookable_type === 'App\\Models\\Subscription') {
                        \Log::debug($reservation->id . ': Is Subscription');
                        $sub = Subscription::where('id', $reservation->bookable_id)->first();

                        if ($sub !== null) {
                            \Log::debug($reservation->id . ': Refunding To ' . $sub->id);
                            $sub->update([
                                'studio_credits' => ($sub->studio_credits + 1),
                            ]);
                        }
                    }

                    if ($reservation->bookable_type === 'App\\Models\\CreditPackPurchase') {
                        \Log::debug($reservation->id . ': Is Credit Pack');
                        $pack = CreditPackPurchase::where('id', $reservation->bookable_id)->first();

                        if ($pack !== null) {
                            \Log::debug($reservation->id . ': Refunding To ' . $pack->id);
                            $pack->update([
                                'studio_credits' => ($pack->studio_credits + 1),
                            ]);
                        }
                    }
                }
            }

            $reservation->update([
                'deleted_by' => auth()->user()->id,
            ]);

            \Log::debug($reservation->id . ': Deleted By ' . auth()->user()->id);

            $reservation->delete();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }
}
