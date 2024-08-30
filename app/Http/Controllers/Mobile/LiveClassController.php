<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\CreditPackPurchase;
use App\Models\LiveClass;
use App\Models\LiveClassBooking;
use App\Models\LiveClassCategory;
use App\Models\LiveClassFavourite;
use App\Models\LiveClassRating;
use App\Models\Reformer;
use App\Models\ReformerBooking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenTok\OpenTok;

class LiveClassController extends Controller
{
    protected $apiKey = '47155174';
    protected $apiSecret = '78ad5f7f7e967192d8fee67385d723875e29a5c9';

    public function categories()
    {
        $categories = LiveClassCategory::get();

        return response()->json($categories);
    }

    public function myBookings()
    {
        $classes = LiveClass::join('live_classes_bookings', 'live_classes_bookings.liveclass_id', 'live_classes.id')
            ->where('member_id', auth()->user()->id)
            ->where('live_classes.datetime', '>=', Carbon::now()->format('Y-m-d') . ' 00:00:00')
            ->whereNull('live_classes_bookings.deleted_at')
            ->with('category')
            ->select('live_classes.*')
            ->orderBy('live_classes.datetime', 'ASC')
            ->get();

        return response()->json($classes);
    }

    public function upcomingClassesByPeriod($period): JsonResponse
    {
        $classes = LiveClass::query();

        if ($period === 'this-week') {
            $classes = LiveClass::where('datetime', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where('datetime', '<=', Carbon::now()->endOfWeek()->format('Y-m-d') . ' 23:59:59');
        }

        if ($period === 'next-week') {
            $classes = LiveClass::where('datetime', '>=',
                Carbon::now()->addWeeks(1)->startOfWeek()->format('Y-m-d') . ' 00:00:00')
                ->where('datetime', '<=', Carbon::now()->addWeeks(1)->endOfWeek()->format('Y-m-d') . ' 23:59:59');
        }

        $classes = $classes->with('category')->get();

        return response()->json($classes);
    }

    public function singleCategory(LiveClassCategory $category): JsonResponse
    {
        $category->upcoming_classes = $category->upcomingClasses;

        return response()->json($category);
    }

    public function singleClass(LiveClass $liveclass): JsonResponse
    {
        $liveclass->equipment = $liveclass->equipment;
        $liveclass->category = $liveclass->category;

        return response()->json($liveclass);
    }

    public function bookingCount(LiveClass $liveclass)
    {
        $count = LiveClassBooking::where('liveclass_id', $liveclass->id)->count();

        return response()->json($count);
    }

    public function averageRating(LiveClass $liveclass)
    {
        $rating = LiveClassRating::join('live_classes', 'live_classes.id', 'live_classes_ratings.liveclass_id')
            ->where('live_classes.category_id', $liveclass->category->id)
            ->avg('rating');

        return response()->json(round($rating));
    }

    public function cancelBooking(LiveClass $liveclass)
    {
        $booking = LiveClassBooking::where('liveclass_id', $liveclass->id)
            ->where('member_id', auth()->user()->id)
            ->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function checkBooking(LiveClass $liveclass)
    {
        $booking = LiveClassBooking::where('liveclass_id', $liveclass->id)
            ->where('member_id', auth()->user()->id)
            ->latest()
            ->first();

        return response()->json($booking);
    }

    public function search(Request $request): JsonResponse
    {
        $classes = LiveClass::query();

        if ($request->category_id != '') {
            $classes = $classes->where('category_id', $request->category_id);
        }

        if ($request->duration != '') {
            $classes = $classes->where('duration', $request->duration);
        }

        if ($request->date != '') {
            $startDate = $request->date . ' 00:00:00';
            $endDate = $request->date . ' 23:59:59';

            $classes = $classes->where('datetime', '>=', $startDate)->where('datetime', '<=', $endDate);
        } else {
            $classes = $classes->where('datetime', '>=', Carbon::now()->format('Y-m-d H:i:s'));
        }

        $classes = $classes->orderBy('datetime', 'ASC')->with('equipment', 'category')->get();

        return response()->json($classes);
    }

    public function openStream(LiveClass $liveclass)
    {
        $opentok = new OpenTok($this->apiKey, $this->apiSecret);

        /**
         * If live class room token is empty, create a new room token and assign.
         */
        if ($liveclass->room_token == '') {
            $opentokSession = $opentok->createSession();
            $roomToken = $opentokSession->getSessionId();

            /**
             * Save the room token to the session.
             */
            $liveclass->update([
                'room_token' => $roomToken,
            ]);
        }

        /**
         * Create a token for client so they can join the session.
         */
        $token = $opentok->generateToken($liveclass->room_token);

        return response()->json([
            'token' => $token,
            'session_id' => $liveclass->room_token,
        ]);
    }

    public function toggleFavourite(LiveClass $liveclass): JsonResponse
    {
        $favourite = LiveClassFavourite::where('user_id', auth()->user()->id)
            ->where('liveclass_id', $liveclass->id)
            ->first();

        if ($favourite === null) {
            LiveClassFavourite::create([
                'user_id' => auth()->user()->id,
                'liveclass_id' => $liveclass->id,
            ]);
        } else {
            $favourite->delete();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }


    public function favouriteIds()
    {
        $favourites = LiveClassFavourite::where('user_id', auth()->user()->id)->get()->pluck('liveclass_id')->toArray();

        return response()->json($favourites);
    }

    public function bookClass(LiveClass $liveclass, Request $request): JsonResponse
    {
        $request->validate([
            'studioOrHome' => 'required|starts_with:home,studio',
            'paymentType' => 'required|starts_with:membership,credit',
            'creditPackPurchaseId' => 'required_if:paymentType,credit',
        ]);

        /**
         * If they are booking a class that needs a reformer,
         * they will need to book a reformer slot.
         */
        if ($request->studioOrHome === 'studio') {
            /**
             * Get a list of machines for this studio which are already booked at this time.
             */
            $bookedMachines = ReformerBooking::select('reformer_bookings.*', 'reformers.gym_id')
                ->where('datetime', $liveclass->datetime)
                ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
                ->where('reformers.gym_id', $request->gym_id)
                ->get()
                ->pluck('reformer_id')
                ->toArray();

            $availableMachine = Reformer::whereNotIn('id', $bookedMachines)
                ->where('gym_id', $request->gym_id)
                ->where('status', 'working')
                ->first();

            if ($availableMachine === null) {
                // There is no machine available at this time.
                return response()->json([
                    'status' => 'failure',
                    'message' => 'We are very sorry, all of our Hebe Pilates machines are all booked up for this time period.',
                ]);
            }

            $liveclassBooking = LiveClassBooking::create([
                'liveclass_id' => $liveclass->id,
                'member_id' => auth()->user()->id,
                'location' => $request->studioOrHome,
            ]);

            ReformerBooking::create([
                'user_id' => auth()->user()->id,
                'reformer_id' => $availableMachine->id,
                'datetime' => $liveclass->datetime,
                'bookable_id' => $liveclassBooking->id,
                'bookable_type' => 'App\Models\LiveClassBooking',
            ]);

            if ($request->paymentType === 'membership') {
                // Deduct a studio credit from this members subscription.
                $sub = auth()->user()->subscription;
                $sub->update([
                    'studio_credits' => ($sub->studio_credits - 1),
                ]);

                $liveclassBooking->update([
                    'booked_using_type' => 'membership',
                    'booked_using_id' => $sub->id,
                ]);
            } else {
                if ($request->paymentType === 'credit') {
                    // Deduct a studio credit from this members credit pack.
                    $creditPackPurchase = CreditPackPurchase::find($request->creditPackPurchaseId);
                    $creditPackPurchase->update([
                        'studio_credits' => ($creditPackPurchase->studio_credits - 1),
                    ]);

                    $liveclassBooking->update([
                        'booked_using_type' => 'credit-pack',
                        'booked_using_id' => $creditPackPurchase->id,
                    ]);
                }
            }
        } else {
            $liveclassBooking = LiveClassBooking::create([
                'liveclass_id' => $liveclass->id,
                'member_id' => auth()->user()->id,
                'location' => $request->studioOrHome,
            ]);

            if ($request->paymentType === 'membership') {
                // Deduct an online credit from this members subscription.
                $sub = auth()->user()->subscription;
                $sub->update([
                    'online_credits' => ($sub->online_credits - 1),
                ]);

                $liveclassBooking->update([
                    'booked_using_type' => 'membership',
                    'booked_using_id' => $sub->id,
                ]);
            } else {
                if ($request->paymentType === 'credit') {
                    // Deduct an online credit from this members credit pack.
                    $creditPackPurchase = CreditPackPurchase::find($request->creditPackPurchaseId);
                    $creditPackPurchase->update([
                        'online_credits' => ($creditPackPurchase->online_credits - 1),
                    ]);

                    $liveclassBooking->update([
                        'booked_using_type' => 'credit-pack',
                        'booked_using_id' => $creditPackPurchase->id,
                    ]);
                }
            }
        }

        SendEmailJob::dispatch($this, 'emails.user.liveclass-booked', 'Booking Accepted',
            ['liveclass' => $liveclass, 'user' => auth()->user()])->onQueue('account-notifications');

        return response()->json([
            'status' => 'success',
        ]);
    }
}
