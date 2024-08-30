<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Gym;
use App\Models\OpeningTime;
use App\Models\Reformer;
use App\Models\ReformerBooking;
use App\Models\User;
use App\Types\BookingTableStatus;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class BookingTableService
{
    /**
     * @param Gym $gym
     * @param CarbonPeriod $period
     *
     * @return array
     */
    public function generateTableForGymForPeriod(Gym $gym, CarbonPeriod $period): array
    {
        $startAndEnd = [
            $period->getStartDate()->startOfDay(),
            $period->getEndDate()?->endOfDay() ?? $period->getStartDate()->endOfDay(),
        ];

        $machines = Reformer::with([
            'bookings' => fn (HasMany $bookings) => $bookings->whereBetween('datetime', $startAndEnd),
            'bookings.member',
            'bookings.reformer',
        ])
            ->where('gym_id', '=', $gym->id)
            ->get();

        $customOpenHours = OpeningTime::where('gym_id', '=', $gym->id)->whereBetween('date', $startAndEnd)->get();

        return array_reduce(
            [
                $this->applyStandardOpeningHours($gym),
                $this->applyCustomOpeningHours($customOpenHours),
                $this->applyBlockedOutStatus($machines),
//                $this->applyTrainerBreaks($gym),
                $this->applyNonWorkingStatus($machines),
                $this->applyBookings($machines->pluck('bookings')->flatten()),
                $this->applyUnavailable(),
            ],
            fn (array $bookingTable, callable $fn): array => $fn($bookingTable),
            $this->initialiseBookingTable($period, $machines),
        );
    }

    /**
     * A set of timeslots for a day, keyed by time and initialised to 'Closed'.
     *
     * @return Collection [
     *      '00:00:00' => 'Closed',
     *      '00:30:00' => 'Closed',
     *      ...
     *  ]
     */
    private function initialiseTimeslots(): Collection
    {
        static $timeslots;

        if ($timeslots === null) {
            $timeslots =
                collect(
                    CarbonPeriod::create(
                        Carbon::now()->startOfDay()->toDateTimeString(),
                        '30 minutes',
                        Carbon::now()->endOfDay()->toDateTimeString(),
                    )->toArray(),
                )->flatMap(
                    fn (CarbonInterface $timeslot): array => [
                        $timeslot->toTimeString() => BookingTableStatus::closed(),
                    ],
                );
        }

        return clone $timeslots;
    }

    /**
     * Generate a list of timeslots with the default set of machines.
     *
     * @param CarbonInterface $day
     * @param Collection<Reformer> $machines
     *
     * @return array [
     *     '00:00:00' => [
     *           [
     *              machineId => 1,
     *              bookingStatus => BookingStatus,
     *              date => '2022-03-29',
     *          ],
     *          ...
     *      ],
     *      ...
     * ]
     */
    private function initialiseDay(CarbonInterface $day, Collection $machines): array
    {
        return
            collect(
                CarbonPeriod::create(
                    Carbon::now()->startOfDay()->toDateTimeString(),
                    '30 minutes',
                    Carbon::now()->endOfDay()->toDateTimeString(),
                )->toArray(),
            )->flatMap(
                fn (CarbonInterface $timeslot): array => [
                    $timeslot->toTimeString() => $machines
                        ->map(
                            fn (Reformer $machine): array => [
                                'machineId' => $machine->id,

                                // Putting the date back in here seems redundant
                                // (and it is), but it makes life easier on the
                                // frontend.
                                'date' => $day->toDateString(),

                                'bookingStatus' => BookingTableStatus::closed(),
                            ],
                        ),
                ],
            )->toArray();
    }

    /**
     * The base booking table with machines, timeslots and default booking.
     *
     * @param CarbonPeriod $period
     * @param Collection<Reformer> $machines
     *
     * @return array [
     *      '2022-03-29' => [
     *          '00:00:00' => [
     *               [
     *                  machineId => 1,
     *                  bookingStatus => BookingStatus,
     *                  date => '2022-03-29',
     *              ],
     *              ...
     *          ],
     *          ...
     *      ],
     *      ...
     *  ]
     */
    private function initialiseBookingTable(CarbonPeriod $period, Collection $machines): array
    {
        return collect($period->toArray())
            ->flatMap(
                fn (CarbonInterface $day): array => [
                    $day->toDateString() => $this->initialiseDay($day, $machines),
                ],
            )->toArray();
    }

    /**
     * @param Gym $gym
     *
     * @return callable
     */
    private function applyStandardOpeningHours(Gym $gym): callable
    {
        return function (array $bookingTable) use ($gym): array {
            $standardOpenHours = $gym->operating_hours;

            foreach ($bookingTable as $day => $timeslots) {
                [
                    $opens,
                    $closes,
                ] = $standardOpenHours->{strtolower(Carbon::parse($day)->format('l'))};

                foreach ($timeslots as $timeslot => $machines) {
                    foreach ($machines as $index => $machine) {
                        if ($timeslot >= $opens && $timeslot < $closes) {
                            $bookingTable[$day][$timeslot][$index]['bookingStatus'] = BookingTableStatus::available();
                        }
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @param Collection<OpeningTime> $customOpenHours
     *
     * @return callable
     */
    private function applyCustomOpeningHours(Collection $customOpenHours): callable
    {
        return function (array $bookingTable) use ($customOpenHours): array {
            foreach ($customOpenHours as $customOpenHour) {
                [
                    'date' => $date,
                    'opening_time' => $opens,
                    'closing_time' => $closes,
                ] = $customOpenHour;

                foreach ($bookingTable[$date] as $timeslot => $machines) {
                    foreach ($machines as $index => $machine) {
                        $newStatus = BookingTableStatus::closed();

                        if ($timeslot >= $opens && $timeslot < $closes) {
                            $newStatus = BookingTableStatus::available();
                        }

                        $bookingTable[$date][$timeslot][$index]['bookingStatus'] = $newStatus;
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @param Collection<Reformer> $machines
     *
     * @return callable
     */
    private function applyBlockedOutStatus(Collection $machines): callable
    {
        return function (array $bookingTable) use ($machines): array {
            foreach ($machines as $machine) {
                $blockedOutBookings = $machine->bookings->filter(
                    fn (ReformerBooking $booking): bool => $booking->bookable_type === 'blocked_out',
                );

                foreach ($blockedOutBookings as $booking) {
                    $datetime = Carbon::parse($booking->datetime);
                    $date = $datetime->format('Y-m-d');
                    $timeslot = $datetime->format('H:i:s');

                    foreach ($bookingTable[$date][$timeslot] as $machineIndex => $machineStatus) {
                        if (
                            $machineStatus['machineId'] === $booking->reformer->id
                            && $machineStatus['bookingStatus']->status !== 'closed'
                        ) {
                            $bookingTable[$date][$timeslot][$machineIndex]['bookingStatus'] = BookingTableStatus::blockedOut();

                            break;
                        }
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @param Collection<Reformer> $machines
     *
     * @return callable
     */
    private function applyNonWorkingStatus(Collection $machines): callable
    {
        return function (array $bookingTable) use ($machines): array {
            $nonWorkingMachines = $machines->filter(
                fn (Reformer $machine): bool => Str::lower($machine->status) !== 'working',
            );

            /** @var Reformer $nonWorkingMachine */
            foreach ($nonWorkingMachines as $nonWorkingMachine) {
                foreach ($bookingTable as $date => $timeslots) {
                    foreach ($timeslots as $timeslot => $machines) {
                        foreach ($machines as $index => $machine) {
                            if (
                                $machine['machineId'] === $nonWorkingMachine->id
                                && $machine['bookingStatus']->status !== 'closed'
                            ) {
                                $bookingTable[$date][$timeslot][$index]['bookingStatus'] =
                                    BookingTableStatus::notWorking(
                                        Str::title(
                                            $nonWorkingMachine->status,
                                        ),
                                    );
                            }
                        }
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @param Collection<ReformerBooking> $bookings
     *
     * @return callable
     */
    private function applyBookings(Collection $bookings): callable
    {
        return function (array $bookingTable) use ($bookings): array {
            $bookings = $bookings->filter(fn (ReformerBooking $booking
            ): bool => $booking->bookable_type !== 'blocked_out');

            /** @var ReformerBooking $booking */
            foreach ($bookings as $booking) {
                $datetime = Carbon::parse($booking->datetime);
                $date = $datetime->format('Y-m-d');
                $timeslot = $datetime->format('H:i:s');

                foreach ($bookingTable[$date][$timeslot] as $machineIndex => $machine) {
                    if ($machine['machineId'] === $booking->reformer->id) {
                        $recentNotes =
                            $booking->member->membersNotes()
                                ->whereDate('updated_at', '>', Carbon::now()->subDays(5))
                                ->get();

                        $newStatus = BookingTableStatus::booking(
                            $booking->member->id,
                            $booking->member->name,
                            $this->isFirstBooking($booking->id, $booking->user_id),
                            $booking->member->studioCreditsRemaining > 0,
                            $this->memberRequiresAttentionBecause($booking->member),
                            $booking->member->parq !== null,
                            $recentNotes->count() > 0,
                        );

                        $bookingTable[$date][$timeslot][$machineIndex]['bookingStatus'] = $newStatus;
                        // These are hour long bookings, so we need to fill the next slot too.
                        $nextTimeslot = $datetime->addMinutes(30)->format('H:i:s');
                        $bookingTable[$date][$nextTimeslot][$machineIndex]['bookingStatus'] = $newStatus;

                        // We've found the booking, so we can move on to the next one.
                        break;
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @return callable
     */
    private function applyUnavailable(): callable
    {
        return function (array $bookingTable): array {
            foreach ($bookingTable as $date => $timeslots) {
                foreach ($timeslots as $timeslot => $machines) {
                    foreach ($machines as $machineIndex => $machine) {
                        if ($machine['bookingStatus']->status === 'available') {
                            $nextTimeslot =
                                Carbon::parse("$date $timeslot")->addMinutes(30)->format('H:i:s');

                            if ($timeslots[$nextTimeslot][$machineIndex]['bookingStatus']->status !== 'available') {
                                $bookingTable[$date][$timeslot][$machineIndex]['bookingStatus'] = BookingTableStatus::unavailable();
                            }
                        }
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @param Gym $gym
     *
     * @return callable
     */
    private function applyTrainerBreaks(Gym $gym): callable
    {
        return function (array $bookingTable) use ($gym): array {
            $breakTimes = $gym->trainer_break_times ?? [];

            foreach ($bookingTable as $date => $timeslots) {
                $dayName = Str::lower(Carbon::parse($date)->dayName);

                if (!array_key_exists($dayName, $breakTimes)) {
                    continue;
                }

                foreach ($breakTimes[$dayName] as $breakTime) {
                    foreach ($bookingTable[$date][$breakTime] as $machineIndex => $machine) {
                        if ($bookingTable[$date][$breakTime][$machineIndex]['bookingStatus']->status !== 'closed') {
                            $bookingTable[$date][$breakTime][$machineIndex]['bookingStatus'] = BookingTableStatus::trainerOnBreak();
                        }
                    }
                }
            }

            return $bookingTable;
        };
    }

    /**
     * @param int $bookingId
     * @param int $userId
     *
     * @return bool
     */
    private function isFirstBooking(int $bookingId, int $userId): bool
    {
        return ReformerBooking::where('user_id', '=', $userId)
                ->orderBy('datetime', 'ASC')
                ->limit(1)
                ->get()
                ->first()
                ->id
            === $bookingId;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    private function memberRequiresAttentionBecause(User $user): array
    {
        $reasons = [];

        if (empty($user->phone_number)) {
            $reasons [] = 'This member does not have a listed phone number';
        }

        if ($user->memberProfile?->preferred_studio_id === null) {
            $reasons [] = 'This member does not have a preferred studio';
        }

        $user->tags
            ->pluck('name')
            ->map([Str::class, 'lower'])
            ->filter(fn (string $name): bool => Str::contains($name, 'high risk'))
            ->each(
                fn (string $name) => array_push(
                    $reasons,
                    "Member has the tag '$name'",
                ),
            );

        return $reasons;
    }
}
