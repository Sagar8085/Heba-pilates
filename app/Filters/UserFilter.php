<?php

namespace App\Filters;

use App\Constants\MembershipTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class UserFilter
 *
 * @package App\Filters;
 */
class UserFilter extends Base
{
    protected array $filters = [
        'ages',
        'tab',
        'creationDateAll',
        'genders',
        'subscriptionStatuses',
        'membershipExpired',
        'creditPackExpired',
        'homeStudio',
        'sessionsCompleted',
        'lastVisit',
        'nextBookedSession',
        'tags',
        'excludedTags',
        'focuses',
        'specificSources',
        'averageVisits',
        'totalCredits',
        'creditExpiry',
        'guestStatus',
        'parqStatus',
        'lastCallDate',
        'lastEmailDate',
        'appointmentDate',
        'lifetimeValue',
        'numberOfCallsMade',
        'numberOfEmailsSent',
        'keywordSearch',
    ];

    public function tab($value): void
    {
        $this->builder->when($value === 'assigned', fn (Builder $query) => $query->withAssignedLeads())
            ->when($value === 'unassigned', fn (Builder $query) => $query->withUnassignedLeads());
    }

    public function ages($value): void
    {
        $ages = collect($value)
            ->extract('name')
            ->removeTextualValues()
            ->map(fn (string $range) => $range === 'Up to 25' ? '0-25' : $range)
            ->flatMap(fn (string $range) => range(...explode('-', $range)));

        $this->builder->when(
            $ages->isNotEmpty(),
            fn (Builder $query) => $query->whereIn('age', $ages)
        );
    }

    public function creationDateAll($value)
    {
        $this->builder->when(!json_decode($value), fn (Builder $query) => $query
            ->where('created_at', '>=', $this->request->input('start_date') . ' 00:00:00')
            ->where('created_at', '<=', $this->request->input('end_date') . ' 23:59:59')
        );
    }

    public function genders($value)
    {
        $genders = collect($value)
            ->extract('slug')
            ->replaceTextNullWithNull();

        $this->builder->when(
            $genders->isNotEmpty(),
            fn (Builder $query) => $query->whereIn('gender', $genders->toArray())
        );
    }

    public function subscriptionStatuses($value)
    {
        $statuses = collect($value)->extract('value');

        $this->builder->when(
            $statuses->isNotEmpty(),
            fn (Builder $query) => $query->whereIn('subscription_status',
                $statuses->map(fn (string $status) => strtolower($status))->toArray())
        );
    }

    public function membershipExpired($value)
    {
        $types = collect($this->request->input('membershipTypes'))->extract('slugs')->flatten();
        $status = collect($value)->extract('value')->first();

        $this->builder->when(
            $types->isNotEmpty(),
            fn (Builder $query) => $query->whereHas('subscriptions', function (Builder $query) use ($status, $types) {
                $query->whereIn('tier', $types->toArray())
                    ->when(
                        $status === MembershipTypes::ACTIVE,
                        fn (Builder $query) => $query->where('expires', '>', now()),
                        fn (Builder $query) => $query->where('expires', '<', now()),
                    );
            })
        );
    }

    public function creditExpiry($value)
    {
        $this->builder->when($value, function (Builder $query) use ($value) {
            $expires = Carbon::parse($value);
            $query->whereHas('subscriptions', fn (Builder $query) => $query->whereDate('expires', $expires))
                ->orWhereHas('creditPackPurchases', fn (Builder $query) => $query->whereDate('expires', $expires));
        });
    }

    public function creditPackExpired($value)
    {
        $types = collect($this->request->input('creditPackTypes'))->extract('ids')->flatten();
        $status = collect($value)->extract('value')->first();

        $this->builder->when(
            $types->isNotEmpty(),
            fn (Builder $query) => $query->whereHas('creditPackPurchases',
                function (Builder $query) use ($status, $types) {
                    $query->whereIn('credit_pack_id', $types->toArray())
                        ->when(
                            $status === MembershipTypes::ACTIVE,
                            fn (Builder $query) => $query->where('expires', '>=', now())->orWhereNull('expires'),
                            fn (Builder $query) => $query->where('expires', '<', now()),
                        );
                })
        );
    }

    public function homeStudio($value)
    {
        $this->builder->whereHas('details', fn (Builder $query) => $query->whereHomeStudioId($value));
    }

    public function sessionsCompleted($value)
    {
        $completedSession = collect($value)
            ->extract('value')
            ->removeTextualValues('any')
            ->first();

        $this->builder->when(
            $completedSession === '0',
            fn (Builder $query) => $query->doesntHave('reformerBookings')
        );

        $this->builder->when(
            str_contains($completedSession, '-'),
            function (Builder $query) use ($completedSession) {
                [$minimum, $maximum] = explode('-', $completedSession);
                return $query->has('reformerBookings', '>=', $minimum)
                    ->has('reformerBookings', '<=', $maximum);
            }
        );

        $this->builder->when(
            $completedSession === '50+',
            fn (Builder $query) => $query->has('reformerBookings', '>=', 50),
        );

        $this->builder->when(
            in_array($completedSession, [1, 2, 3, 4]),
            fn (Builder $query) => $query->has('reformerBookings', '=', $completedSession),
        );
    }

    public function lastVisit($value)
    {
        $lastVisit = collect($value)->extract('value')->first();

        $this->builder->when(
            $lastVisit === 'none',
            fn (Builder $query) => $query->doesntHave('recentReformerBooking'),
        );

        $this->builder->when(
            $lastVisit === 'range',
            function (Builder $query) {
                return $query->whereHas('recentReformerBooking', function ($query) {
                    $query->where('datetime', '>=', $this->request->input('lastVisitStartDate') . ' 00:00:00')
                        ->where('datetime', '<=', $this->request->input('lastVisitEndDate') . ' 23:59:59');
                })->whereDoesntHave('reformerBookings', function ($query) {
                    $query->whereBetween('datetime', [
                        now(),
                        Carbon::parse($this->request->input('lastVisitEndDate')),
                    ]);
                });
            },
        );
    }

    public function nextBookedSession($value)
    {
        $nextBookedSession = collect($value)->extract('value')->first();

        $this->builder->when(
            $nextBookedSession === 'none',
            fn (Builder $query) => $query->doesntHave('upcomingReformerBooking'),
        );

        $this->builder->when(
            $nextBookedSession === 'range',
            function (Builder $query) {
                return $query->whereHas('upcomingReformerBooking', function ($query) {
                    $query->where('datetime', '>=', $this->request->input('nextBookedSessionStartDate') . ' 00:00:00')
                        ->where('datetime', '<=', $this->request->input('nextBookedSessionEndDate') . ' 23:59:59');
                })->whereDoesntHave('reformerBookings', function ($query) {
                    $query->whereBetween('datetime',
                        [Carbon::parse($this->request->input('nextBookedSessionStartDate')), now()]);
                });
            },
        );
    }

    public function tags($value)
    {
        $tagIds = collect($value)->extract('id')->toArray();

        $this->builder->whereHas(
            'tags',
            fn (Builder $query) => $query->whereIn('tag_id', $tagIds)
        );
    }

    public function excludedTags($value)
    {
        $tagIds = collect($value)->extract('id')->toArray();

        $this->builder->whereHas(
            'tags',
            fn (Builder $query) => $query->whereNotIn('tag_id', $tagIds)
        );
    }

    public function focuses($value)
    {
        $focusIds = collect($value)
            ->extract('id')
            ->toArray();

        $this->builder->whereHas(
            'focuses',
            fn (Builder $query) => $query->whereIn('focus.id', $focusIds)
        );
    }

    public function specificSources($value)
    {
        $this->builder->when(
            json_decode($value),
            fn (Builder $query) => $query->whereHas('lead', function ($query) {
                $query->whereIn(
                    'source',
                    collect($this->request->input('leadSources'))->extract('value')->toArray()
                );
            })
        );
    }

    public function averageVisits($value)
    {
        $averageVisits = collect($value)->extract('value')->toArray();

        $this->builder->whereHas('reformerBookings', function ($query) use ($averageVisits) {
            $query->selectRaw('COUNT(reformer_bookings.id)/4 as avg_visits_per_week')
                ->whereBetween('datetime', [
                    now()->subDays(28),
                    now(),
                ])
                ->when(
                    $averageVisits == '1-2',
                    fn (Builder $query) => $query->havingBetween('avg_visits_per_week', [1, 2]),
                    function (Builder $query) use ($averageVisits) {
                        return $query->having(
                            'avg_visits_per_week',
                            $averageVisits === 1 ? '<' : '>=',
                            $averageVisits
                        );
                    }
                );
        });
    }

    public function totalCredits($value)
    {
        $totalCredits = collect($value)->extract('value')->first();

        if ($totalCredits === 'any') {
            return;
        }

        $creditPacks = DB::table('credit_packs_purchases')
            ->select('user_id', 'expires', DB::raw('SUM(studio_credits) as credit_pack_credits'))
            ->where('expires', '>', now())
            ->groupBy('user_id');

        $subscriptions = DB::table('subscriptions')
            ->select('user_id', 'expires', DB::raw('SUM(studio_credits) as subscription_credits'))
            ->where('expires', '>', now())
            ->groupBy('user_id');

        $this->builder->selectRaw('credit_pack_credits, subscription_credits, SUM(COALESCE(credit_pack_credits, 0)+COALESCE(subscription_credits, 0)) as total_credits')
            ->leftJoinSub($creditPacks, 'credit_packs', function ($join) {
                $join->on('users.id', 'credit_packs.user_id');
            })
            ->leftJoinSub($subscriptions, 'join_subscriptions', function ($join) {
                $join->on('users.id', 'join_subscriptions.user_id');
            });

        $this->builder->when(
            $totalCredits === '2-4',
            fn (Builder $query) => $query->havingBetween('total_credits', [2, 4])
        );

        $this->builder->when(
            $totalCredits <= 1,
            fn (Builder $query) => $query->having('total_credits', $totalCredits)
        );

        $this->builder->when(
            $totalCredits === 5,
            fn (Builder $query) => $query->having('total_credits', '>=', 5)
        );
    }

    public function guestStatus($value)
    {
        $this->builder->whereIn('guest_status', collect($value)->extract('value')->toArray());
    }

    public function lastCallDate($value)
    {
        $lastCallDate = collect($value)->extract('value')->first();

        $this->builder->when(
            $lastCallDate === 'none',
            fn (Builder $query) => $query->whereDoesntHave('leadActivityCalls')
        );

        $this->builder->when(
            $lastCallDate === 'range',
            fn (Builder $query) => $query->with('leadActivityCalls')
                ->whereHas('leadActivityCalls', fn (Builder $query) => $query->whereBetween(
                    'datetime',
                    [
                        $this->request->input('lastCallStartDate') . ' 00:00:00',
                        $this->request->input('lastCallEndDate') . ' 23:59:59',
                    ]
                )
                    ->whereDoesntHave('leadActivityCalls', fn (Builder $query) => $query->whereBetween(
                        'datetime',
                        [
                            $this->request->input('lastCallEndDate') . ' 23:59:59',
                            now(),
                        ]
                    ))
                )
        );
    }

    public function lastEmailDate($value)
    {
        $lastEmailDate = collect($value)->extract('value')->first();

        $this->builder->when(
            $lastEmailDate === 'none',
            fn (Builder $query) => $query->whereDoesntHave('lead.leadAppointment')
        );

        $this->builder->when(
            $lastEmailDate === 'range',
            fn (Builder $query) => $query->with('lead.leadEmails')
                ->whereHas('lead.leadEmails', function ($query) {
                    $query->whereBetween('created_at', [
                        $this->request->input('lastEmailStartDate') . ' 00:00:00',
                        $this->request->input('lastEmailEndDate') . ' 23:59:59',
                    ]);
                })
                ->whereDoesntHave('lead.leadEmails', function ($query) {
                    $query->whereBetween('created_at', [
                        $this->request->input('lastEmailEndDate') . ' 23:59:59',
                        now(),
                    ]);
                })
        );
    }

    public function appointmentDate($value)
    {
        $appointmentDate = collect($value)->extract('value')->first();

        $this->builder->when(
            $appointmentDate === 'none',
            fn (Builder $query) => $query->whereDoesntHave('lead.leadAppointment')
        );

        $this->builder->when(
            $appointmentDate === 'range',
            fn (Builder $query) => $query->with('lead.leadAppointment')
                ->whereHas('lead.leadAppointment', function ($query) {
                    $query->whereBetween('datetime', [
                        $this->request->input('appointmentStartDate') . ' 00:00:00',
                        $this->request->input('appointmentEndDate') . ' 23:59:59',
                    ]);
                })
                ->whereDoesntHave('lead.leadAppointment', function ($query) {
                    $query->whereBetween('datetime', [
                        $this->request->input('appointmentEndDate') . ' 23:59:59',
                        now(),
                    ]);
                })
        );
    }

    public function lifetimeValue($value)
    {
        $lifetimeValue = collect($value)->extract('value')->first();

        $this->builder->when(
            $lifetimeValue !== 'none',
            function (Builder $query) use ($lifetimeValue) {
                $query = $query->withSum('orders as total_spend', 'value');
                if ($lifetimeValue === '100-5000') {
                    return $query->having('total_spend', '>', 100)
                        ->having('total_spend', '<', 5000);
                }

                return $query->having('total_spend', '>', 5000);
            },
            fn (Builder $query) => $query->whereDoesntHave('orders')
        );
    }

    public function numberOfCallsMade($value)
    {
        $numberOfCallsMade = collect($value)->extract('value')->first();

        $this->builder->when(
            $numberOfCallsMade === '0-0',
            fn (Builder $query) => $query->whereDoesntHave('leadActivityCalls'),
            function (Builder $query) use ($numberOfCallsMade) {
                [$min, $max] = explode('-', $numberOfCallsMade);

                $query->withCount('leadCalls as total_calls')
                    ->having('total_calls', '>=', $min)
                    ->having('total_calls', '<=', $max);
            }
        );
    }

    public function numberOfEmailsSent($value)
    {
        $numberOfEmailsSent = collect($value)->extract('value')->first();

        $this->builder->when(
            $numberOfEmailsSent === '0-0',
            fn (Builder $query) => $query->whereDoesntHave('lead.leadEmails'),
            function (Builder $query) use ($numberOfEmailsSent) {
                [$min, $max] = explode('-', $numberOfEmailsSent);

                $query->withCount('sentEmails as total_emails')
                    ->having('total_emails', '>=', $min)
                    ->having('total_emails', '<=', $max);
            }
        );
    }

    public function keywordSearch($value)
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('users.id', $value)
                ->orWhere('users.first_name', 'like', '%' . $value . '%')
                ->orWhere('users.last_name', 'like', '%' . $value . '%')
                ->orWhere('users.email', 'like', '%' . $value . '%')
                ->orWhereRaw("concat(first_name, ' ', last_name) like '%" . $value . "%' ");
        });
    }

    public function parqStatus($value)
    {
        $status = collect($value)->extract('value')->first();

        $this->builder
            ->when($status === 'completed', fn (Builder $query) => $query->whereHas('parq'))
            ->when($status === 'not-completed', fn (Builder $query) => $query->whereDoesntHave('parq'));
    }
}
