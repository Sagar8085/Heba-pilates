<?php

namespace Tests\Http\Controllers\Dashboard;

use App\Models\Lead;
use App\Models\Order;
use Tests\TestCase;

/**
 * Class TotalRevenueChartControllerTest
 *
 * @package Tests\Http\Controllers\Dashboard
 */
class TotalRevenueChartControllerTest extends TestCase
{
    /**
     * @test
     * @dataProvider records
     */
    public function it_gets_all_chart_payloads(callable $orders, string $type, array $result): void
    {
        $this->setActualTime();

        $orders();

        $this->assertSame(
            $result,
            $this->signIn()
                ->getJson(route('revenue.index', [
                    'report_date_from' => now()->subWeek()->toDateString(),
                    'report_date_to' => now()->addDay()->toDateString(),
                ]))
                ->json($type . '.chart')
        );
    }

    /**
     * @test
     * @dataProvider emptyRecords
     */
    public function it_gets_all_empty_chart_payloads(callable $orders, string $type, array $result): void
    {
        $this->setActualTime();

        $orders();

        $this->assertSame(
            $result,
            $this->signIn()
                ->getJson(route('revenue.index', [
                    'report_date_from' => now()->subWeek()->toDateString(),
                    'report_date_to' => now()->addDay()->toDateString(),
                ]))
                ->json($type . '.chart')
        );
    }

    public function emptyRecords(): array
    {
        return [
            [
                fn () => Order::all(),
                'promoConversions',
                [
                    'labels' => [
                        'No Conversion',
                        'Converted to subscription',
                        'Converted to credit pack',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                0,
                                0,
                                0,
                            ],
                            'backgroundColor' => [
                                '#17B5C8',
                                '#ef5998',
                                '#8860ae',
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'volumeOfCreditPackSales',
                [
                    'labels' => [
                        '30 Pack',
                        '10 Pack',
                        '1 Credit',
                        'Free Session',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                            ],
                            'backgroundColor' => [
                                '#ef5998',
                                '#8860ae',
                                '#ffad89',
                                '#17B5C8',
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'creditPackRenewals',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Credit Pack Renewals',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'creditPackNew',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'New Credit Packs',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'creditPackChurn',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Credit Pack Churn',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'pendingSubscriptionRevenue',
                [
                    'labels' => [
                        'Unlimited Annual VIPs',
                        'Unlimited Monthly',
                        'Premium Monthly',
                        'Standard Monthly',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                            ],
                            'backgroundColor' => [
                                '#ef5998',
                                '#8860ae',
                                '#ffad89',
                                '#17B5C8',
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'volumeOfSubscriptionSales',
                [
                    'labels' => [
                        'Unlimited Annual VIPs',
                        'Unlimited Monthly',
                        'Premium Monthly',
                        'Standard Monthly',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                            ],
                            'backgroundColor' => [
                                '#ef5998',
                                '#8860ae',
                                '#ffad89',
                                '#17B5C8',
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'purchaseTypeBreakdown',
                [
                    'labels' => [
                        'Promo Purchases',
                        'Subscriptions Purchases',
                        'New Subscriptions Purchases',
                        'Credit Pack Purchases',
                        'Free Credits/Packs/Plans',
                        'VIP/Partner Credit Plans',
                        'Corporate Credit Plans',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                            ],
                            'backgroundColor' => [
                                '#475fc6',
                                '#adc911',
                                '#16b5c8',
                                '#8860ae',
                                '#ffad89',
                                '#ffda88',
                                '#ef5998',
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'averageYield',
                [
                    'labels' => [

                    ],
                    'datasets' => [
                        [
                            'label' => 'Mean Average Charge per Guest',
                            'data' => [0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'allSubscriptions',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'All Subscriptions Revenue',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'totalRevenue',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Total Revenue by Volume of Sales',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'allCreditPacks',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'All Credit Pack Revenue',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'allPromos',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'All Promo Revenue',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'subscriptionRenewals',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Subscription Renewals',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'subscriptionNew',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'New Subscriptions',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'subscriptionChurn',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Subscription Churn',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function records(): array
    {
        return [
            [
                fn () => $this->createOrdersForPreviousNumberOfDays(7, [
                    'member_id' => $this->createAdministrator()->getKey(),
                    'value' => 1000000,
                ]),
                'averageYield',
                [
                    'labels' => [
                        '04/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Mean Average Charge per Guest',
                            'data' => [
                                '10,000.00',
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrdersForPreviousNumberOfDays(7, [
                    'member_id' => $this->createAdministrator()->getKey(),
                    'value' => 1000000,
                ]),
                'allSubscriptions',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'All Subscriptions Revenue',
                            'data' => [
                                0,
                                10000,
                                10000,
                                10000,
                                10000,
                                10000,
                                10000,
                                10000,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrdersForPreviousNumberOfDays(8, [
                    'value' => 1000.00,
                ]),
                'totalRevenue',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Total Revenue by Volume of Sales',
                            'data' => [
                                10,
                                10,
                                10,
                                10,
                                10,
                                10,
                                10,
                                10,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForCreditPack([
                    'value' => 1000.00,
                ], 8),
                'allCreditPacks',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'All Credit Pack Revenue',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                80,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForSubscription([
                    'promo_code' => 'random_promo_code',
                    'value' => 1000.00,
                    'created_at' => now()->subDays(3),
                ], 5),
                'allPromos',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'All Promo Revenue',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                50,
                                0,
                                0,
                                0,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForSubscription([
                    'value' => 1000,
                ], 9),
                'subscriptionChurn',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Subscription Churn',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                90,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForUnlimitedMonthlySubscription([
                    'value' => 6500,
                ], 7),
                'subscriptionRenewals',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Subscription Renewals',
                            'data' => [0, 0, 0, 0, 0, 0, 0, 455, 0],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForNotRenewedSubscription([
                    'value' => 8100,
                    'created_at' => now()->subDays(6),
                ], 8),
                'subscriptionNew',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'New Subscriptions',
                            'data' => [0, 648, 0, 0, 0, 0, 0, 0, 0],
                        ],
                    ],
                ],
            ],
            [
                function () {
                    $this->createOrderForVipSubscription([], 2);
                    $this->createOrderForUnlimitedMonthlySubscription([], 3);
                    $this->createOrderForPremiumMonthlySubscription([], 6);
                    $this->createOrderForStandardMembership([], 7);

                    return Order::all();
                },
                'volumeOfSubscriptionSales',
                [
                    'labels' => [
                        'Unlimited Annual VIPs',
                        'Unlimited Monthly',
                        'Premium Monthly',
                        'Standard Monthly',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                2,
                                3,
                                6,
                                7,
                            ],
                            'backgroundColor' => [
                                '#ef5998',
                                '#8860ae',
                                '#ffad89',
                                '#17B5C8',
                            ],
                        ],
                    ],
                ],
            ],
            [
                function () {
                    $this->createOrderForVipSubscription([
                        'value' => 10000,
                    ], 113);
                    $this->createOrderForUnlimitedMonthlySubscription([
                        'value' => 20000,
                    ], 89);
                    $this->createOrderForPremiumMonthlySubscription([
                        'value' => 30000,
                    ], 34);
                    $this->createOrderForStandardMembership([
                        'value' => 40000,
                    ], 103);

                    return Order::all();
                },
                'pendingSubscriptionRevenue',
                [
                    'labels' => [
                        'Unlimited Annual VIPs',
                        'Unlimited Monthly',
                        'Premium Monthly',
                        'Standard Monthly',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                11300,
                                17800,
                                10200,
                                41200,
                            ],
                            'backgroundColor' => [
                                '#ef5998',
                                '#8860ae',
                                '#ffad89',
                                '#17B5C8',
                            ],
                        ],
                    ],
                ],
            ],
            [
                function () {
                    $member = $this->createAdministrator()->memberProfile;

                    $creditPack = $this->createCreditPack(null, [
                        'promotional' => false,
                    ]);

                    $this->createOrderForCreditPack(
                        [
                            'expires' => now()->subDays(8),
                            'member_id' => $member->id,
                        ],
                        1,
                        $creditPack
                    );

                    /** @var Order $order */
                    $this->createOrderForCreditPack(
                        [
                            'expires' => now(),
                            'member_id' => $member->id,
                            'value' => 11500,
                        ],
                        1,
                        $creditPack
                    );
                },
                'creditPackRenewals',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'Credit Pack Renewals',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                115,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForCreditPack([
                    'value' => 450,
                    'created_at' => now()->subDays(2),
                ], 9),
                'creditPackNew',
                [
                    'labels' => [
                        '03/01',
                        '04/01',
                        '05/01',
                        '06/01',
                        '07/01',
                        '08/01',
                        '09/01',
                        '10/01',
                        '11/01',
                    ],
                    'datasets' => [
                        [
                            'label' => 'New Credit Packs',
                            'data' => [
                                0,
                                0,
                                0,
                                0,
                                0,
                                40.5,
                                0,
                                0,
                                0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                function () {
                    $this->createOrderForThirtyPackCreditGuests([], 3);
                    $this->createOrderForTenPackCreditGuests([], 6);
                    $this->createOrderForOnePackCreditGuests([], 1);
                    $this->createOrderForIntroPackCreditGuests([], 12);

                    return Order::all();
                },
                'volumeOfCreditPackSales',
                [
                    'labels' => [
                        '30 Pack',
                        '10 Pack',
                        '1 Credit',
                        'Free Session',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                3,
                                6,
                                1,
                                12,
                            ],
                            'backgroundColor' => [
                                '#ef5998',
                                '#8860ae',
                                '#ffad89',
                                '#17B5C8',
                            ],
                        ],
                    ],
                ],
            ],
            [
                function () {
                    $this->createLead(2, [
                        'assigned_to' => null,
                        'created_at' => now(),
                    ]);

                    $this->createLead(3, [
                        'subscribe_weekly' => 1,
                        'created_at' => now(),
                    ]);

                    $this->createLead(6, [
                        'subscribe_monthly' => 0,
                        'subscribe_weekly' => 0,
                        'created_at' => now(),
                    ]);

                    return Lead::all();
                },
                'promoConversions',
                [
                    'labels' => [
                        'No Conversion',
                        'Converted to subscription',
                        'Converted to credit pack',
                    ],
                    'datasets' => [
                        [
                            'data' => [
                                2,
                                3,
                                6,
                            ],
                            'backgroundColor' => [
                                '#17B5C8',
                                '#ef5998',
                                '#8860ae',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
