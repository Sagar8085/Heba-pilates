<?php

namespace Tests\Http\Controllers\Dashboard;

use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class TotalRevenueMetricControllerTest
 *
 * @package Tests\Http\Controllers\Dashboard
 */
class TotalRevenueMetricControllerTest extends TestCase
{
    /**
     * @test
     * @dataProvider metrics
     */
    public function it_gets_all_metric_payloads(callable $orders, string $type, array $result): void
    {
        $orders();

        $this->assertSame(
            $result,
            $this->signIn()
                ->getJson(route('revenue.index', [
                    'report_date_from' => now()->subWeek()->toDateString(),
                    'report_date_to' => now()->addDay()->toDateString(),
                ]))
                ->json($type . '.metrics')
        );

    }

    /**
     * @return array[]
     */
    public function metrics(): array
    {
        return [
            [
                fn () => Order::all(),
                'creditPackRenewals',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'creditPackNew',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'creditPackChurn',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'averageYield',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'allSubscriptions',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'totalRevenue',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'allCreditPacks',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'allPromos',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'subscriptionRenewals',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'subscriptionNew',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => Order::all(),
                'subscriptionChurn',
                [
                    [
                        'value' => '£0.00 (0)',
                    ],
                ],
            ],
            [
                fn () => $this->createOrdersForPreviousNumberOfDays(7, [
                    'member_id' => $this->createAdministrator()->getKey(),
                    'value' => 1000000,
                ]),
                'averageYield',
                [
                    [
                        'value' => '£10,000.00 (7)',
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
                    [
                        'value' => '£70,000.00 (7)',
                    ],
                ],
            ],
            [
                function () {
                    collect(range(1, 4))
                        ->each(fn () => $this->createOrdersForPreviousNumberOfDays(7, [
                            'value' => 1000.00,
                        ]));
                },
                'totalRevenue',
                [
                    [
                        'value' => '£280.00 (28)',
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForCreditPack([
                    'value' => 1000.00,
                ], 30),
                'allCreditPacks',
                [
                    [
                        'value' => '£300.00 (30)',
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForSubscription([
                    'promo_code' => 'random_promo_code',
                    'value' => 1000.00,
                ], 30),
                'allPromos',
                [
                    [
                        'value' => '£300.00 (30)',
                    ],
                ],
            ],
            [
                function () {
                    return $this->createOrderForRenewedSubscription([
                        'promo_code' => 'random_promo_code',
                        'value' => 10000,
                    ], 9);
                },
                'subscriptionRenewals',
                [
                    [
                        'value' => '£900.00 (9)',
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForNotRenewedSubscription([
                    'value' => 10000,
                ], 9),
                'subscriptionNew',
                [
                    [
                        'value' => '£900.00 (9)',
                    ],
                ],
            ],
            [
                function () {
                    return $this->createOrderForSubscription([
                        'value' => 10000,
                    ], 9);
                },
                'subscriptionChurn',
                [
                    [
                        'value' => '£900.00 (9)',
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForCreditPack([
                    'value' => 10000,
                ], 9),
                'creditPackRenewals',
                [
                    [
                        'value' => '£900.00 (9)',
                    ],
                ],
            ],
            [
                fn () => $this->createOrderForCreditPack([
                    'value' => 10000,
                ], 9),
                'creditPackNew',
                [
                    [
                        'value' => '£900.00 (9)',
                    ],
                ],
            ],
            [
                function () {
                    $creditPack = $this->createCreditPack(null, [
                        'promotional' => false,
                    ]);

                    $this->createOrderForCreditPack([
                        'value' => 10000,
                        'expires' => now()->subDays(14),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 20000,
                        'expires' => now()->subDays(13),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 30000,
                        'expires' => now()->subDays(12),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 40000,
                        'expires' => now()->subDays(11),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 50000,
                        'expires' => now()->subDays(10),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 60000,
                        'expires' => now()->subDays(9),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 70000,
                        'expires' => now()->subDays(8),
                    ], 1, $creditPack);

                    $this->createOrderForCreditPack([
                        'value' => 80000,
                        'expires' => now()->subDays(7),
                    ], 1, $creditPack);
                },
                'creditPackChurn',
                [
                    [
                        'value' => '£3,600.00 (8)',
                    ],
                ],
            ],
        ];
    }
}
