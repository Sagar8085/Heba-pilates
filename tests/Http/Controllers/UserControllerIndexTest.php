<?php

namespace Tests\Http\Controllers;

use App\Models\CreditPackPurchase;
use App\Models\Member;
use App\Models\PARQ;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\PARQFactory;
use Database\Seeders\FocusSeeder;
use Database\Seeders\UserTagSeeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\Traits\HandlesGenders;
use Tests\Traits\HandlesParqStatuses;
use Tests\Traits\HandlesSubscriptionStatuses;
use Tests\Traits\HandlesUsers;

/**
 * Class UserControllerIndexTest
 *
 * @package Tests\Http\Controllers
 */
class UserControllerIndexTest extends ControllerTest
{
    use HandlesUsers;
    use HandlesGenders;
    use HandlesSubscriptionStatuses;
    use HandlesParqStatuses;

    /** @test */
    public function it_cannot_be_accessed_if_unauthorised(): void
    {
        $this->getJson(route('guest.index'))->assertUnauthorized();
    }

    /** @test */
    public function it_gets_all_guest_records_except_for_the_currently_authenticated_user(): void
    {
        $this->index('guest.index')->assertJson([
            'data' => [],
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => null,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 0,
                'total' => 0,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_paginated_into_twenty(): void
    {
        $users = $this->createUser([], 20);

        $this->index('guest.index')
            ->assertJson([
                'data' => $this->mapUsersToPayload($users->take(20)),
                'links' => [
                    'first' => 'http://localhost/api/admin/guest?page=1',
                    'last' => 'http://localhost/api/admin/guest?page=1',
                    'prev' => null,
                    'next' => null,
                ],
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 1,
                    'links' => [
                        [
                            'url' => null,
                            'label' => '&laquo; Previous',
                            'active' => false,
                        ],
                        [
                            'url' => 'http://localhost/api/admin/guest?page=1',
                            'label' => '1',
                            'active' => true,
                        ],
                        [
                            'url' => null,
                            'label' => 'Next &raquo;',
                            'active' => false,
                        ],
                    ],
                    'path' => 'http://localhost/api/admin/guest',
                    'per_page' => 20,
                    'to' => 20,
                    'total' => 20,
                ],
            ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_age(): void
    {
        $this->createUser([], 5);

        $users = $this->createUser([
            'age' => 20,
        ], 10);

        $this->index('guest.index', [
            'ages' => [
                '{"slug":"0-25","name":"Up to 25"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider genders
     */
    public function it_gets_all_guest_records_filtered_by_gender(string|null $gender, string $input): void
    {
        $this->createUser([], 20);

        $users = $this->createUser(compact('gender'), 10);

        $this->index('guest.index', [
            'genders' => [
                $input,
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_account_creation_date(): void
    {
        $this->createUser([
            'created_at' => Carbon::create(2022, 1),
        ], 20);

        $users = $this->createUser([
            'created_at' => Carbon::create(2022, 2, 10),
        ], 10);

        $this->index('guest.index', [
            'creationDateAll' => false,
            'start_date' => Carbon::create(2022, 2)->format('Y-m-d'),
            'end_date' => Carbon::create(2022, 2)->endOfMonth()->format('Y-m-d'),
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider subscriptionStatuses
     */
    public function it_gets_all_guest_records_filtered_by_subscription_statuses(
        string|null $subscription_status,
        string $input
    ): void {
        $this->createUser([], 20);

        $users = $this->createUser(compact('subscription_status'), 10);

        $this->index('guest.index', [
            'subscriptionStatuses' => [
                $input,
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_membership_types_and_active(): void
    {
        $subscriptions = $this->createSubscription(5, [
            'tier' => 'unlimited-membership',
            'expires' => now()->addWeek(),
        ]);

        $users = $subscriptions->map(function (Subscription $subscription) {
            $subscription->user->memberProfile()->create([
                'contract_path' => Str::random(),
            ]);
            return $subscription->user->fresh();
        });

        $this->createSubscription(10, [
            'tier' => 'premium',
            'expires' => now()->subYear(),
        ]);

        $this->index('guest.index', [
            'membershipExpired' => '{"name":"Active","value":"active"}',
            'membershipTypes' => [
                '{"slugs":["unlimited-membership","unlimited-membership-subscription","unlimited-membership-2","unlimited-membership-subscription-2"],"name":"Unlimited","slug":"unlimited"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 5,
                'total' => 5,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_membership_types_and_expired(): void
    {
        $this->createSubscription(10, [
            'tier' => 'once-weekly',
            'expires' => now()->subYear(),
        ]);

        $subscriptions = $this->createSubscription(5, [
            'tier' => 'premium-membership',
            'expires' => now()->subWeek(),
        ]);

        $users = $subscriptions->map(function (Subscription $subscription) {
            $subscription->user->memberProfile()->create([
                'contract_path' => Str::random(),
            ]);
            return $subscription->user->fresh();
        });

        $this->index('guest.index', [
            'membershipExpired' => '{"name":"Expired","value":"expired"}',
            'membershipTypes' => [
                '{"slugs":["premium","premium-membership","premium-membership-subscription"],"name":"Premium","slug":"premium"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 5,
                'total' => 5,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_home_studio_id(): void
    {
        $gym = $this->createGym();
        $users = $this->createMember(10, [
            'home_studio_id' => $gym->getKey(),
        ])->map(fn (Member $member) => $member->user);

        $this->createMember(10);

        $this->index('guest.index', [
            'homeStudio' => $gym->getKey(),
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_fifty_sessions_completed(): void
    {
        $users = $this->createUser([
            'guest_status' => 'Prospect',
            'created_at' => now(),
            'updated_at' => now(),
        ], 20);

        $this->createReformerBooking(50, [
            'user_id' => $users->first()->getKey(),
        ]);

        $this->index('guest.index', [
            'sessionsCompleted' => '{"name":"50+","value":"50+"}',
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users->take(1)),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 1,
                'total' => 1,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_thirty_to_fifty_sessions_completed(): void
    {
        $users = $this->createUser([
            'guest_status' => 'Prospect',
        ], 20);

        $this->createReformerBooking(35, [
            'user_id' => $users->first()->getKey(),
        ]);

        $this->createReformerBooking(45, [
            'user_id' => $users->skip(1)->first()->getKey(),
        ]);

        $this->index('guest.index', [
            'sessionsCompleted' => '{"name":"30-50","value":"30-50"}',
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users->take(2)),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 2,
                'total' => 2,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_four_sessions_completed(): void
    {
        $users = $this->createUser([
            'guest_status' => 'Prospect',
        ], 20);

        $this->createReformerBooking(4, [
            'user_id' => $users->first()->getKey(),
        ]);

        $this->createReformerBooking(4, [
            'user_id' => $users->skip(1)->first()->getKey(),
        ]);

        $this->createReformerBooking(4, [
            'user_id' => $users->skip(2)->first()->getKey(),
        ]);

        $this->index('guest.index', [
            'sessionsCompleted' => '{"name":"4","value":"4"}',
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users->take(3)),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 3,
                'total' => 3,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_zero_sessions_completed(): void
    {
        $user = $this->createUser();
        $users = $this->createUser([], 19);

        $this->createReformerBooking(1, [
            'user_id' => $user->getKey(),
        ]);

        $this->index('guest.index', [
            'sessionsCompleted' => '{"name":"0","value":"0"}',
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users->fresh()),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 19,
                'total' => 19,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_last_visit(): void
    {
        $user = $this->createUser();
        $this->createUser([], 19);

        $this->createReformerBooking(1, [
            'user_id' => $user->getKey(),
            'datetime' => Carbon::create(2022, 2, 10),
        ]);

        $this->index('guest.index', [
            'lastVisit' => '{"name":"Date Range","value":"range"}',
            'lastVisitStartDate' => Carbon::create(2022, 2)->format('Y-m-d'),
            'lastVisitEndDate' => Carbon::create(2022, 2, 30)->format('Y-m-d'),
        ])->assertJson([
            'data' => $this->mapUsersToPayload(
                User::query()->whereId($user->getKey())->get()
            ),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 1,
                'total' => 1,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_no_previous_visit(): void
    {
        tap($this->createUser([], 10), fn (Collection $users) => $users->each(function (User $user) {
            $this->createReformerBooking(1, [
                'user_id' => $user->getKey(),
                'datetime' => now()->addDay(),
            ]);
        }));

        $this->index('guest.index', [
            'lastVisit' => '{"name":"No Past Visits","value":"none"}',
        ])->assertJson([
            'data' => [],
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => null,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 0,
                'total' => 0,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_next_visit(): void
    {
        $users = tap($this->createUser([], 10), fn (Collection $users) => $users->each(function (User $user) {
            $this->createReformerBooking(1, [
                'user_id' => $user->getKey(),
                'datetime' => now()->addDay(),
            ]);
        }));

        $this->index('guest.index', [
            'nextBookedSession' => '{"name":"Date Range","value":"range"}',
            'nextBookedSessionStartDate' => now()->format('Y-m-d'),
            'nextBookedSessionEndDate' => now()->addDays(2)->format('Y-m-d'),
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_no_next_visit(): void
    {
        tap($this->createUser([], 10), fn (Collection $users) => $users->each(function (User $user) {
            $this->createReformerBooking(1, [
                'user_id' => $user->getKey(),
                'datetime' => now()->addDay(),
            ]);
        }));

        $this->index('guest.index', [
            'nextBookedSession' => '{"name":"No Future Bookings","value":"none"}',
        ])->assertJson([
            'data' => [],
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => null,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => null,
                'total' => 0,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_tags(): void
    {
        $this->seed(UserTagSeeder::class);

        $this->createUser([], 10);

        $users = tap($this->createUser([], 10), fn (Collection $users) => $users->each(function (User $user) {
            $user->tags()->sync([1, 3, 4]);
        }));

        $this->index('guest.index', [
            'tags' => [
                '{"id":1,"name":"Medical Condition - Asthma","slug":"medical-condition-asthma","created_at":"2021-04-21T17:21:39.000000Z","updated_at":"2021-04-21T17:21:39.000000Z"}',
                '{"id":3,"name":"Heart","slug":"heart","created_at":"2021-05-06T09:56:11.000000Z","updated_at":"2021-05-06T09:56:11.000000Z"}',
                '{"id":4,"name":"Injury","slug":"injury","created_at":"2021-05-19T05:50:36.000000Z","updated_at":"2021-05-19T05:50:36.000000Z"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_excluded_tags(): void
    {
        $this->seed(UserTagSeeder::class);

        $users = tap($this->createUser([], 10), function (Collection $users) {
            $users->each(function (User $user) {
                $user->tags()->syncWithoutDetaching([1]);
            });
            $users->skip(5)->each(function (User $user) {
                $user->tags()->sync([3, 4]);
            });
        });

        $this->index('guest.index', [
            'excludedTags' => [
                '{"id":3,"name":"Heart","slug":"heart","created_at":"2021-05-06T09:56:11.000000Z","updated_at":"2021-05-06T09:56:11.000000Z"}',
                '{"id":4,"name":"Injury","slug":"injury","created_at":"2021-05-19T05:50:36.000000Z","updated_at":"2021-05-19T05:50:36.000000Z"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users->take(5)),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 5,
                'total' => 5,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_focus(): void
    {
        $this->seed(FocusSeeder::class);

        $this->createUser([], 10);

        $users = tap($this->createUser([], 10), function (Collection $users) {
            $users->each(function (User $user) {
                $user->focuses()->syncWithoutDetaching([1]);
            });
        });

        $this->index('guest.index', [
            'focuses' => [
                '{"id":1,"name":"Body shape and tone","slug":"general","created_at":"2021-03-10T10:52:38.000000Z","updated_at":"2021-03-10T10:52:38.000000Z"}',
                '{"id":4,"name":"Energy and mood","slug":"mobility","created_at":"2021-03-10T10:52:38.000000Z","updated_at":"2021-03-10T10:52:38.000000Z"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_lead_source(): void
    {
        $this->createUser([], 10);

        $users = $this->createLead(10, [
            'source' => 'website',
        ])->map->user;

        $this->index('guest.index', [
            'specificSources' => true,
            'leadSources' => [
                '{"name":"Website","value":"website"}',
                '{"name":"Social Media","value":"social-media"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider parqStatuses
     */
    public function it_gets_all_guest_records_filtered_by_parq_statuses(
        string|null $status,
        string $input
    ): void {
        $usersWithParq = $this->createUser([], 10);
        $usersWithoutParq = $this->createUser([], 10);

        $usersWithParq->each(fn (User $user) => $user->parq()->save(
            PARQ::factory()->create(['user_id' => $user->id, 'created_by' => $user->id])
        ));

        $this->index('guest.index', [
            'parqStatus' => [
                $input,
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($status === 'completed' ? $usersWithParq : $usersWithoutParq),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_subscription_credit_expiry(): void
    {
        Subscription::factory()->count(10)->create([
            'expires' => now()->addDay(),
        ]);
        $users = Subscription::factory()->count(10)->create([
            'expires' => now(),
        ])->map->user;

        $this->index('guest.index', [
            'creditExpiry' => now()->toISOString(),
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_purchase_credit_expiry(): void
    {
        $this->createUser([], 10);
        $users = $this->createUser([], 10);
        $users->each(
            fn (User $user) => $user->creditPackPurchases()->save(
                CreditPackPurchase::factory()->create([
                    'order_id' => 1,
                    'expires' => now(),
                ])
            )
        );

        $this->index('guest.index', [
            'creditExpiry' => now()->toISOString(),
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 10,
                'total' => 10,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_credit_pack_types_and_active(): void
    {
        $purchases = $this->createCreditPackPurchase(5, [
            'credit_pack_id' => 15,
            'expires' => now(),
        ]);

        $users = $purchases->map(
            fn (CreditPackPurchase $purchase) => $purchase->member
        );

        $this->index('guest.index', [
            'membershipExpired' => '{"name":"Active","value":"active"}',
            'creditPackTypes' => [
                '{"name":"Intro Pack","ids":[15]}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 5,
                'total' => 5,
            ],
        ]);
    }

    /** @test */
    public function it_gets_all_guest_records_filtered_by_credit_pack_types_and_expired(): void
    {
        $this->createSubscription(10, [
            'tier' => 'once-weekly',
            'expires' => now()->subYear(),
        ]);

        $subscriptions = $this->createSubscription(5, [
            'tier' => 'premium-membership',
            'expires' => now()->subWeek(),
        ]);

        $users = $subscriptions->map(function (Subscription $subscription) {
            $subscription->user->memberProfile()->create([
                'contract_path' => Str::random(),
            ]);
            return $subscription->user->fresh();
        });

        $this->index('guest.index', [
            'membershipExpired' => '{"name":"Expired","value":"expired"}',
            'membershipTypes' => [
                '{"slugs":["premium","premium-membership","premium-membership-subscription"],"name":"Premium","slug":"premium"}',
            ],
        ])->assertJson([
            'data' => $this->mapUsersToPayload($users),
            'links' => [
                'first' => 'http://localhost/api/admin/guest?page=1',
                'last' => 'http://localhost/api/admin/guest?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    [
                        'url' => 'http://localhost/api/admin/guest?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'path' => 'http://localhost/api/admin/guest',
                'per_page' => 20,
                'to' => 5,
                'total' => 5,
            ],
        ]);
    }
}
