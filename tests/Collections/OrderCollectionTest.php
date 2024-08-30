<?php

namespace Tests\Collections;

use App\Collections\OrderCollection;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class OrderCollectionTest
 *
 * @package Tests\Collections
 */
class OrderCollectionTest extends TestCase
{
    /** @test */
    public function it_can_get_you_a_total_value_of_all_orders(): void
    {
        $this->createOrdersForPreviousNumberOfDays(7, [
            'value' => 5000,
        ]);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertEquals(350, $orders->totalValue());
    }

    /** @test */
    public function it_can_filter_orders_with_promo_codes(): void
    {
        $this->createOrder(26, [
            'promo_code' => null,
        ]);

        $this->createOrder(25, [
            'promo_code' => 'example promo code',
        ]);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(25, $orders->withPromoCode());
    }

    /** @test */
    public function it_can_filter_orders_without_promo_codes(): void
    {
        $this->createOrder(26, [
            'promo_code' => null,
        ]);

        $this->createOrder(25, [
            'promo_code' => 'example promo code',
        ]);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(26, $orders->withoutPromoCode());
    }

    /** @test */
    public function it_can_filter_recently_renewed_subscription_orders(): void
    {
        $this->createOrderForRenewedSubscription([], 26);
        $this->createOrderForNotRenewedSubscription([], 34);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(26, $orders->recentlyRenewedSubscriptions());
    }

    /** @test */
    public function it_can_filter_not_recently_renewed_subscription_orders(): void
    {
        $this->createOrderForRenewedSubscription([], 26);
        $this->createOrderForNotRenewedSubscription([], 34);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(34, $orders->notRecentlyRenewedSubscriptions());
    }

    /** @test */
    public function it_can_filter_credit_pack_orders(): void
    {
        $this->createOrderForRenewedSubscription([], 26);
        $this->createOrderForCreditPack([], 24);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(24, $orders->creditPacksOnly());
    }

    /** @test */
    public function it_can_filter_subscription_orders(): void
    {
        $this->createOrderForRenewedSubscription([], 71);
        $this->createOrderForCreditPack();

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(71, $orders->subscriptionsOnly());
    }

    /** @test */
    public function it_can_filter_subscription_orders_with_tiers(): void
    {
        $this->createOrderForVipSubscription([], 53);
        $this->createOrderForRenewedSubscription([], 26);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(53, $orders->withSubscriptionTiers([Subscription::VIP_UNLIMITED]));
    }

    /** @test */
    public function it_can_filter_subscription_orders_without_tiers(): void
    {
        $this->createOrderForVipSubscription([], 53);
        $this->createOrderForRenewedSubscription([], 26);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(
            26,
            $orders->withoutSubscriptionTiers([
                Subscription::VIP_UNLIMITED,
            ])
        );
    }

    /** @test */
    public function it_can_filter_subscription_orders_of_thirty_credit_packs(): void
    {
        $this->createOrderForThirtyPackCreditGuests([], 50);
        $this->createOrderForTenPackCreditGuests([], 50);
        $this->createOrderForSubscription([], 50);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(50, $orders->withThirtyPackCredits());
    }

    /** @test */
    public function it_can_filter_subscription_orders_of_ten_credit_packs(): void
    {
        $this->createOrderForTenPackCreditGuests([], 50);
        $this->createOrderForSubscription([], 50);
        $this->createOrderForVipSubscription([], 50);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(50, $orders->withTenPackCredits());
    }

    /** @test */
    public function it_can_filter_subscription_orders_of_one_credit_packs(): void
    {
        $this->createOrderForOnePackCreditGuests([], 50);
        $this->createOrderForSubscription([], 50);
        $this->createOrderForVipSubscription([], 50);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(50, $orders->withOnePackCredits());
    }

    /** @test */
    public function it_can_filter_subscription_orders_of_intro_credit_packs(): void
    {
        $this->createOrderForIntroPackCreditGuests([], 50);
        $this->createOrderForSubscription([], 50);
        $this->createOrderForVipSubscription([], 50);

        /** @var OrderCollection $orders */
        $orders = Order::all();

        $this->assertCount(50, $orders->withIntroPackCredits());
    }

    /**
     * @test
     * @dataProvider meanAverages
     */
    public function it_can_calculate_mean_order_averages(callable $preparation, array $result): void
    {
        /** @var OrderCollection $orders */
        $orders = $preparation();

        $this->assertEquals($result, $orders->getMeanOrderValues()->toArray());
    }

    /**
     * @test
     * @dataProvider meanAverages
     */
    public function it_can_calculate_mean_order_average_totals(callable $preparation, array $result): void
    {
        /** @var OrderCollection $orders */
        $orders = $preparation();

        $this->assertEquals(array_sum($result), $orders->getMeanOrderAverages()->roundSum());
    }

    /**
     * @test
     * @dataProvider meanAverages
     */
    public function it_can_calculate_average_of_mean_order_average_totals(callable $preparation, array $result): void
    {
        /** @var OrderCollection $orders */
        $orders = $preparation();

        $this->assertEquals(collect($result)->avg(), round($orders->getAverageOfMeanOrderAverages(), 2));
    }

    /**
     * @return array
     */
    public function meanAverages(): array
    {
        return [
            [
                fn () => Order::all(),
                [0],
            ],
            [
                fn () => $this->createOrdersForPreviousNumberOfDays(10, [
                    'value' => 50000,
                ]),
                [500, 500, 500, 500, 500, 500, 500, 500, 500, 500],
            ],
            [
                function () {
                    $first = $this->createAdministrator();
                    $second = $this->createAdministrator();
                    $this->createOrdersForPreviousNumberOfDays(10, [
                        'member_id' => $first->getKey(),
                        'value' => 50000,
                    ]);

                    $this->createOrdersForPreviousNumberOfDays(10, [
                        'member_id' => $second->getKey(),
                        'value' => 60000,
                    ]);

                    return Order::all();
                },
                [500, 600],
            ],
            [
                function () {
                    $first = $this->createAdministrator();

                    $this->createOrdersForPreviousNumberOfDays(10, [
                        'member_id' => $first->getKey(),
                        'value' => 50000,
                    ]);

                    $this->createOrdersForPreviousNumberOfDays(10, [
                        'member_id' => $first->getKey(),
                        'value' => 60000,
                    ]);

                    return Order::all();
                },
                [550],
            ],
            [
                function () {
                    collect(range(1, 3))
                        ->map(fn () => $this->createAdministrator())
                        ->each(function (User $user) {
                            return $this->createOrdersForPreviousNumberOfDays(7, [
                                'member_id' => $user->getKey(),
                                'value' => 100,
                            ])->merge(
                                $this->createOrdersForPreviousNumberOfDays(5, [
                                    'member_id' => $user->getKey(),
                                    'value' => 2500,
                                ])
                            )->merge(
                                $this->createOrdersForPreviousNumberOfDays(21, [
                                    'member_id' => $user->getKey(),
                                    'value' => 60000,
                                ])
                            )->merge(
                                $this->createOrdersForPreviousNumberOfDays(212, [
                                    'member_id' => $user->getKey(),
                                    'value' => 60000,
                                ])
                            );
                        })->flatten();

                    return Order::all();
                },
                [571.15, 571.15, 571.15],
            ],
        ];
    }

    /** @test */
    public function it_can_sum_and_round_a_collection_of_numbers(): void
    {
        $this->assertEquals(
            1050.22,
            OrderCollection::make([
                10.24243243244234324,
                912.12374723312,
                19.43,
                36.41,
                72.01,
            ])->roundSum()
        );
    }

    /** @test */
    public function it_can_get_the_mean_unique_mean_average_dates(): void
    {
        $this->setActualTime(2022, 3, 7);

        $orders = $this->createOrdersForPreviousNumberOfDays(10, [
            'value' => 50000,
        ]);

        $this->assertEquals(
            [
                '26/02',
                '27/02',
                '28/02',
                '01/03',
                '02/03',
                '03/03',
                '04/03',
                '05/03',
                '06/03',
                '07/03',
            ],
            $orders->getMeanOrderAverageDates()
        );
    }

    /** @test */
    public function it_can_get_all_renewals_for_an_order_collection(): void
    {
        $orders = collect(range(1, 6))
            ->map(function (int $range) {
                $creditPack = $this->createCreditPack(null, [
                    'promotional' => false,
                ]);

                $member = $this->createAdministrator()->memberProfile;

                $this->createOrderForCreditPack([
                    'expires' => Carbon::create(2022, 07, 01),
                    'member_id' => $member->id,
                ], 1, $creditPack);

                return $this->createOrderForCreditPack([
                    'expires' => Carbon::create(2022, 07, 8)->subDays($range),
                    'member_id' => $member->id,
                ], 1, $creditPack);
            })
            ->flatten(2)
            ->pipe(fn ($collection) => OrderCollection::make($collection));

        $this->assertCount(
            6,
            $orders->renewalsOnly()
        );
    }

    /** @test */
    public function it_can_get_all_non_renewals_for_an_order_collection(): void
    {
        $orders = collect(range(1, 6))
            ->map(function (int $range) {
                $creditPack = $this->createCreditPack(null, [
                    'promotional' => false,
                ]);

                $member = $this->createAdministrator()->memberProfile;

                return $this->createOrderForCreditPack([
                    'expires' => Carbon::create(2022, 07, 8)->subDays($range),
                    'member_id' => $member->id,
                ], 1, $creditPack);
            })
            ->flatten(2)
            ->pipe(fn ($collection) => OrderCollection::make($collection));

        $this->assertCount(
            6,
            $orders->nonRenewalsOnly()
        );
    }

    /** @test */
    public function it_can_filter_the_first_time_purchases(): void
    {
        $creditPack = $this->createCreditPack(null, [
            'promotional' => false,
        ]);

        $member = $this->createAdministrator()->memberProfile;

        $this->createOrderForCreditPack([
            'expires' => Carbon::create(2022, 07, 01),
            'member_id' => $member->id,
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'expires' => Carbon::create(2022, 07, 5),
            'member_id' => $member->id,
        ], 1, $creditPack);


        $member = $this->createAdministrator()->memberProfile;

        $this->createOrderForCreditPack([
            'expires' => Carbon::create(2022, 07, 01),
            'member_id' => $member->id,
        ], 1, $creditPack);

        $this->assertCount(1, Order::all()->firstTimePurchasesOnly());
    }
}
