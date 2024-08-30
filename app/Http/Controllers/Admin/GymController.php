<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditPackPurchase;
use App\Models\Gym;
use App\Models\OpeningTime;
use App\Models\Reformer;
use App\Models\ReformerBooking;
use App\Models\Subscription;
use App\Services\BookingTableService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use Log;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\Response;

class GymController extends Controller
{
    /**
     * Load all of this users accessible gyms.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Gym::select('gyms.*')->filterAccess()->orderBy('name', 'ASC')->get());
    }

    /**
     * Load a single gym resource.
     * @param Gym $gym
     *
     * @return JsonResponse
     */
    public function single(Gym $gym)
    {
        /** The admin might have permission to view gyms via the Gate, but now we need to check they have access to this specific one. */
        $gymIds = auth()->user()->accessibleGymsArray();

        if (!in_array($gym->id, $gymIds)) {
            abort(403);
        }

        return response()->json($gym->load([
            'reformers',
        ]));
    }

    /**
     * Store Opening Times for the Gym
     * @param Request $request
     * @param Gym $gym
     *
     * @return JsonResponse
     */
    public function opening(Request $request, Gym $gym)
    {
        $hours = [
            'friday' => [
                $request->fridayopen,
                $request->fridayclose,
            ],
            'saturday' => [
                $request->saturdayopen,
                $request->saturdayclose,
            ],
            'sunday' => [
                $request->sundayopen,
                $request->sundayclose,
            ],
            'monday' => [
                $request->mondayopen,
                $request->mondayclose,
            ],
            'tuesday' => [
                $request->tuesdayopen,
                $request->tuesdayclose,
            ],
            'wednesday' => [
                $request->wednesdayopen,
                $request->wednesdayclose,
            ],
            'thursday' => [
                $request->thursdayopen,
                $request->thursdayclose,
            ],
        ];

        Gym::where('id', '=', $gym->id)
            ->update(['operating_hours' => $hours]);

        return response()->json([
            'hours' => $hours,
        ]);

    }

    /**
     * Delete Custom Opening Times
     * @param Request $request
     * @param Gym $gym
     *
     * @return JsonResponse
     */
    public function openingCustomDelete(Request $request, Gym $gym)
    {
        OpeningTime::where('id', '=', $request->id)
            ->delete();

        return response()->json();

    }

    /**
     * Save Custom Opening Times
     * @param Request $request
     * @param Gym $gym
     *
     * @return JsonResponse
     */
    public function openingCustomSave(Request $request, Gym $gym)
    {
        OpeningTime::create([
            'date' => $request->date,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'gym_id' => $gym->id,
            'closed' => $request->openStatus === 'no' ? 1 : 0,
        ]);

        return response()->json();

    }

    /**
     * Return all the machines and their booked reservations.
     *
     * @param Gym $gym
     * @param Request $request
     * @param BookingTableService $bookingTableService
     *
     * @return JsonResponse
     */
    public function machineReservations(
        Gym $gym,
        Request $request,
        BookingTableService $bookingTableService,
    ): JsonResponse {
        return response()->json(
            $bookingTableService->generateTableForGymForPeriod(
                $gym,
                CarbonPeriod::create(
                    $request->get('start', Carbon::now()->format('Y-m-d')),
                    $request->get('end', Carbon::now()->format('Y-m-d')),
                ),
            ),
        );
    }

    /**
     * Return a single machine and it's booked booked reservations for this week.
     * @param Gym $gym
     * @param Reformer $machine
     * @return JsonResponse
     */
    public function singleMachineReservations(Gym $gym, Reformer $machine)
    {
        $machines = Reformer::where('gym_id', $gym->id)->get();
        $data = collect();
        $dates = [];

        $date = Carbon::now();

        if (request('paginationType') === 'next') {
            $date = Carbon::parse(request('date_end'));
            $date = $date->addDays(1);
        }

        if (request('paginationType') === 'previous') {
            $date = Carbon::parse(request('date_start'));
            $date = $date->subDays(7);
        }

        for ($i = 0; $i <= 6; $i++) {
            $loopDate = Carbon::parse($date);
            $loopDate->addDays($i);
            array_push($dates, $loopDate->format('Y-m-d'));
            $endDate = $loopDate->format('Y-m-d');
            $endDateHuman = $loopDate->format('l dS F Y');
        }

        $startDate = $date->format('Y-m-d');
        $startDateHuman = $date->format('l dS F Y');

        foreach ($dates as $date) {
            $slots = [];

            $bookings = ReformerBooking::where('reformer_id', $machine->id)
                ->where('datetime', '>=', $date . ' 00:00:00')
                ->where('datetime', '<=', $date . ' 23:59:59')
                ->get();

            foreach ($bookings as $booking) {
                $time = Carbon::parse($booking->datetime)->format('Gi');
                $slots[$time] = [
                    'available' => false,
                    'date_human' => $booking->date_human,
                    'time_human' => $booking->time_human,
                    'booking' => [
                        'id' => $booking->id,
                        'member' => [
                            'id' => $booking->member->id,
                            'name' => $booking->member->name,
                        ],
                    ],
                ];
                $time = Carbon::parse($booking->datetime)->addMinutes(30)->format('Gi');
                $slots[$time] = [
                    'available' => false,
                    'date_human' => $booking->date_human,
                    'time_human' => $booking->time_human,
                    'booking' => [
                        'id' => $booking->id,
                        'member' => [
                            'id' => $booking->member->id,
                            'name' => $booking->member->name,
                        ],
                    ],
                ];
            }

            $data->push([
                'date' => $date,
                'date_human' => $date,
                'status' => ucwords($machine->status),
                'timeslots' => $slots,
            ]);
        }

        $datings = collect([
            'date_start' => $startDate,
            'date_end' => $endDate,
            'date_start_human' => $startDateHuman,
            'date_end_human' => $endDateHuman,
        ]);

        return response()->json([
            'data' => $data,
            'date_start' => $startDate,
            'date_end' => $endDate,
            'dates' => $datings,
        ]);
    }

    public function singleReservation(ReformerBooking $booking)
    {
        return response()->json($booking);
    }

    public function deleteReservation(ReformerBooking $booking)
    {
        Log::debug($booking->id . ': To Be Deleted');

        if ($booking->bookable_type) {
            if ($booking->datetime > date('Y-m-d H:i:s')) {
                Log::debug($booking->id . ': Can Be Refunded');
                if ($booking->bookable_type === 'App\\Models\\Subscription') {
                    Log::debug($booking->id . ': Is Subscription');
                    $sub = Subscription::where('id', $booking->bookable_id)->first();

                    if ($sub !== null) {
                        Log::debug($booking->id . ': Refunding To ' . $sub->id);
                        $sub->update([
                            'studio_credits' => ($sub->studio_credits + 1),
                        ]);
                    }
                }

                if ($booking->bookable_type === 'App\\Models\\CreditPackPurchase') {
                    Log::debug($booking->id . ': Is Credit Pack');
                    $pack = CreditPackPurchase::where('id', $booking->bookable_id)->first();

                    if ($pack !== null) {
                        Log::debug($booking->id . ': Refunding To ' . $pack->id);
                        $pack->update([
                            'studio_credits' => ($pack->studio_credits + 1),
                        ]);
                    }
                }
            }
        }

        $booking->update([
            'deleted_by' => auth()->user()->id,
        ]);

        Log::debug($booking->id . ': Deleted By ' . auth()->user()->id);

        $booking->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function allReservations(Request $request)
    {
        $reservations = ReformerBooking::latest()->filterAccess()->notBlocked()
            ->with('member', 'reformer', 'deleter')
            ->select('reformer_bookings.*', 'gyms.name as gym_name', 'gyms.id as gym_id')
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->join('gyms', 'gyms.id', 'reformers.gym_id');

        if ($request->gyms) {
            $gymIds = collect($request->gyms)->pluck('id')->toArray();
            $reservations = $reservations->whereIn('gyms.id', $gymIds);
        }

        if (!$request->allDates) {
            $reservations = $reservations->where('reformer_bookings.datetime', '>=', $request->startDate . ' 00:00:00')
                ->where('reformer_bookings.datetime', '<=', $request->endDate . ' 23:59:59');
        }

        if (!$request->createdOnAllDates) {
            $reservations = $reservations->where('reformer_bookings.created_at', '>=',
                $request->createdOnStartDate . ' 00:00:00')
                ->where('reformer_bookings.created_at', '<=', $request->createdOnEndDate . ' 23:59:59');
        }

        if (request('download') === 'true') {
            $reservations = $reservations->get();

            // Create CSV in Memory
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add Headers
            $csv->insertOne([
                'Member',
                'Gym',
                'Machine',
                'Booking Date',
                'Booking Time',
                'Booking Created',
            ]);

            // Add Results
            foreach ($reservations as $reservation) {
                $csv->insertOne([
                    $reservation->member->name,
                    $reservation->gym_name,
                    $reservation->reformer->name,
                    $reservation->date_formatted,
                    $reservation->time_human,
                    $reservation->created_at_human,
                ]);
            }

            $csv->output('export.csv');
            die;
        }

        $reservations = $reservations->withTrashed()->paginate(50);

        return response()->json($reservations);
    }

    public function upcomingReservations(Request $request)
    {
        $today = Carbon::now();
        $reservations = ReformerBooking::orderBy('datetime', 'ASC')
            ->filterAccess()
            ->notBlocked()
            ->with('member', 'reformer', 'deleter')
            ->select('reformer_bookings.*', 'gyms.name as gym_name', 'gyms.id as gym_id')
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->join('gyms', 'gyms.id', 'reformers.gym_id');

        $reservations = $reservations->where('reformer_bookings.datetime', '>=', '' . $today . '');

        if ($request->gyms) {
            $gymIds = collect($request->gyms)->pluck('id')->toArray();
            $reservations = $reservations->whereIn('gyms.id', $gymIds);
        }

        if (!$request->allDates) {
            $reservations = $reservations->where('reformer_bookings.datetime', '>=', $request->startDate . ' 00:00:00')
                ->where('reformer_bookings.datetime', '<=', $request->endDate . ' 23:59:59');
        }

        if (!$request->createdOnAllDates) {
            $reservations = $reservations->where('reformer_bookings.created_at', '>=',
                $request->createdOnStartDate . ' 00:00:00')
                ->where('reformer_bookings.created_at', '<=', $request->createdOnEndDate . ' 23:59:59');
        }

        if (request('download') === 'true') {
            $reservations = $reservations->get();

            // Create CSV in Memory
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add Headers
            $csv->insertOne([
                'Member',
                'Gym',
                'Machine',
                'Booking Date',
                'Booking Time',
                'Booking Created',
            ]);

            // Add Results
            foreach ($reservations as $reservation) {
                $csv->insertOne([
                    $reservation->member->name,
                    $reservation->gym_name,
                    $reservation->reformer->name,
                    $reservation->date_formatted,
                    $reservation->time_human,
                    $reservation->created_at_human,
                ]);
            }

            $csv->output('export.csv');
            die;
        }

        $reservations = $reservations->withTrashed()->paginate(50);

        return response()->json($reservations);
    }

    public function reserveSlot(Request $request)
    {
        $creditMethod = $request->creditMethod;
        $credit = explode(':', $creditMethod);

        $paymentType = $credit[0];
        $paymentId = $credit[1];

        $reservation = ReformerBooking::create([
            'user_id' => $request->member_id,
            'reformer_id' => $request->machine_id,
            'datetime' => $request->date . ' ' . $request->time . ':00',
        ]);

        if ($paymentType === 'membership') {
            $sub = Subscription::where('id', $paymentId)->first();
            $sub->update([
                'studio_credits' => ($sub->studio_credits - 1),
            ]);

            $reservation->update([
                'bookable_type' => 'App\Models\Subscription',
                'bookable_id' => $sub->id,
            ]);
        } else {
            if ($paymentType === 'credit') {
                $creditPackPurchase = CreditPackPurchase::find($paymentId);
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
        ]);
    }

    /**
     * Mark a machine as out-of-action/unbookable for a given period.
     *
     * @param Gym $gym
     * @param Reformer $machine
     * @param string $startDatetime
     * @param string $endDatetime
     *
     * @return JsonResponse
     */
    public function blockMachine(Gym $gym, Reformer $machine, string $startDatetime, string $endDatetime): JsonResponse
    {
        $period = CarbonPeriod::create($startDatetime, '30 minutes', $endDatetime);

        // We need to separate blocked out bookings and member bookings. If
        // there are any member bookings, we can't block out the machine for this
        // time period. We'll use the list of blocked out bookings to allow
        // adding blocks around existing blocks, e.g. the machine is already
        // blocked out between 1300 and 1400, and the request is to block 1100 to
        // 1700.
        /**
         * @var Collection<ReformerBooking> $blockedBookings
         * @var Collection<ReformerBooking> $memberBookings
         */
        [
            $blockedBookings,
            $memberBookings,
        ] = $machine->bookings()
            ->whereBetween(
                'datetime',
                [
                    $period->getStartDate()->toDateTimeString(),
                    $period->getEndDate()?->toDateTimeString() ?? $period->getStartDate()->toDateTimeString(),
                ],
            )
            ->get()
            ->partition(fn (ReformerBooking $booking): bool => $booking->bookable_type === 'blocked_out');

        if ($memberBookings->count() > 0) {
            return response()->json(
                'Cannot block out a machine for a period that already has bookings',
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $alreadyBlockedDates = $blockedBookings->pluck('datetime');

        DB::table('reformer_bookings')->insert(
            array_map(
                fn (CarbonInterface $date): array => [
                    'reformer_id' => $machine->id,
                    'datetime' => $date->toDateTimeString(),
                    'bookable_type' => 'blocked_out',
                    'created_at' => $date->toDateTimeString(),
                    'updated_at' => $date->toDateTimeString(),
                ],
                array_filter(
                    $period->toArray(),
                    fn (CarbonInterface $date): bool => !$alreadyBlockedDates->contains($date->toDateTimeString()),
                ),
            ),
        );

        return response()->json();
    }

    /**
     * Remove block status from machine.
     *
     * @param Gym $gym
     * @param Reformer $machine
     * @param string $startDatetime
     * @param string $endDatetime
     *
     * @return JsonResponse
     */
    public function freeMachine(Gym $gym, Reformer $machine, string $startDatetime, string $endDatetime): JsonResponse
    {
        $machine->bookings()
            ->whereBetween('datetime', [$startDatetime, $endDatetime])
            ->where('bookable_type', '=', 'blocked_out')
            ->delete();

        return response()->json();
    }
}
