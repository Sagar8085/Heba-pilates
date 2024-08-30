<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\CreditPackPurchase;
use App\Models\LiveClass;
use App\Models\LiveClassBooking;
use App\Models\LiveClassCategory;
use App\Models\LiveClassFavourite;
use App\Models\LiveClassRating;
use App\Models\LiveClassTag;
use App\Models\Reformer;
use App\Models\ReformerBooking;
use App\Models\Subscription;
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

    /**
     * Load my live class bookings.
     */
    public function myBookings()
    {
        $classes = LiveClass::join('live_classes_bookings', 'live_classes_bookings.liveclass_id', 'live_classes.id')
            ->where('member_id', auth()->user()->id)
            ->whereNull('live_classes_bookings.deleted_at')
            ->where('datetime', '>=', Carbon::now()->format('Y-m-d') . ' 00:00:00')
            ->with('category', 'equipment')
            ->select('live_classes.*')
            ->orderBy('datetime', 'ASC')
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

        $classes = $classes->with('category', 'equipment')->orderBy('datetime', 'ASC')->get();

        return response()->json($classes);
    }

    public function book(LiveClass $liveclass, Request $request): JsonResponse
    {

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

        SendEmailJob::dispatch($this, 'emails.user.liveclass-booked', 'Booking Accepted', [
            'liveclass' => $liveclass,
            'location' => $request->studioOrHome,
            'user' => auth()->user(),
        ])->onQueue('account-notifications');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function streamLiveClass(LiveClass $liveclass): JsonResponse
    {
        $token = '';
        $sessionId = '';
        $type = 'viewer';

        $opentok = new OpenTok($this->apiKey, $this->apiSecret);

        if (!$liveclass->can_join_stream || $liveclass->room_token == '') {
            $secondsTillStream = Carbon::now()->diffInSeconds($liveclass->datetime, false);
            $startTime = Carbon::parse($liveclass->datetime)->format('F d, Y H:i:s');
            return view('webportal.liveclass-waiting-room', compact('secondsTillStream', 'startTime'));
        }

        /**
         * If live class room token is empty, create a new room token and assign.
         */
        if ($liveclass->room_token == '') {

            $opentokSession = $opentok->createSession();
            $roomToken = $opentokSession->getSessionId();

            $liveclass->update([
                'room_token' => $roomToken,
            ]);
        }

        if ($type === 'member') {
            $data = 'type=member';
        } else {
            $data = 'type=trainer';
        }

        if ($liveclass->room_token != '') {
            $opentok = new OpenTok($this->apiKey, $this->apiSecret);
            $token = $opentok->generateToken($liveclass->room_token, [
                'data' => $data,
            ]);
            $sessionId = $liveclass->room_token;

            return view('webportal.liveclass', compact('token', 'sessionId', 'type'));
        }

        echo 'Nothing To See Here';
        die;
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
        SendEmailJob::dispatch($this, 'emails.user.liveclass-cancelled', 'Booking Cancelled',
            ['liveclass' => $liveclass, 'user' => auth()->user()])->onQueue('account-notifications');

        $booking = LiveClassBooking::where('liveclass_id', $liveclass->id)
            ->where('member_id', auth()->user()->id)
            ->first();

        if ($booking->booked_using_type === 'membership') {
            $refundModel = Subscription::find($booking->booked_using_id);
        } else {
            if ($booking->booked_using_type === 'credit-pack') {
                $refundModel = CreditPackPurchase::find($booking->booked_using_id);
            }
        }

        if (isset($refundModel)) {
            if ($booking->location === 'home') {
                $refundModel->update([
                    'online_credits' => ($refundModel->online_credits + 1),
                ]);
            } else {
                $refundModel->update([
                    'studio_credits' => ($refundModel->studio_credits + 1),
                ]);
            }
        }

        $booking->delete();

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

    public function favourites()
    {
        $ids = LiveClassFavourite::where('user_id', auth()->user()->id)->get()->pluck('liveclass_id')->toArray();
        $favourites = LiveClass::whereIn('id', $ids)->with('equipment')->get();

        return response()->json($favourites);
    }

    public function favouritesByID()
    {
        $favourites = LiveClassFavourite::where('user_id', auth()->user()->id)->get()->pluck('liveclass_id')->toArray();

        return response()->json($favourites);
    }

    public function recommended()
    {
        $topTags = LiveClassTag::select('_pivot_live_classes_tags.*',
            \DB::raw('COUNT(_pivot_live_classes_tags.tag_id) as totalTags'))
            ->join('live_classes_bookings', 'live_classes_bookings.liveclass_id',
                '_pivot_live_classes_tags.liveclass_id')
            ->where('live_classes_bookings.member_id', auth()->user()->id)
            ->groupBy('tag_id')
            ->orderBy('totalTags', 'DESC')
            ->take(1)
            ->get()->pluck('tag_id')->toArray();

        $classes = LiveClass::select('live_classes.*')
            ->join('_pivot_live_classes_tags', '_pivot_live_classes_tags.liveclass_id', 'live_classes.id')
            ->whereIn('_pivot_live_classes_tags.tag_id', $topTags)
            ->with('category', 'equipment')
            ->where('datetime', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->get();

        return response()->json($classes);
    }

    public function search(Request $request)
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
}
