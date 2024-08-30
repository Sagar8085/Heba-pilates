<?php

namespace Tests\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class TotalRevenueControllerTest
 *
 * @package Tests\Http\Controllers\Dashboard
 */
class TotalRevenueControllerTest extends TestCase
{
    /** @test */
    public function it_cannot_be_accessed_if_unauthorised(): void
    {
        $this->getJson(route('revenue.index'))->assertUnauthorized();
    }

    /** @test */
    public function it_returns_the_correct_keys(): void
    {
        $response = $this->signIn()->getJson(route('revenue.index'))->json();

        $this->assertSame(
            [
                'totalRevenue',
                'averageYield',
                'purchaseTypeBreakdown',
                'allSubscriptions',
                'allCreditPacks',
                'allPromos',
                'subscriptionRenewals',
                'subscriptionNew',
                'subscriptionChurn',
                'standardMonthlySubscribers',
                'volumeOfSubscriptionSales',
                'pendingSubscriptionRevenue',
                'creditPackRenewals',
                'creditPackNew',
                'creditPackChurn',
                'volumeOfCreditPackSales',
                'promoConversions',
            ],
            array_keys($response)
        );
    }

    /** @test */
    public function it_gets_the_total_revenue_chart_for_a_single_day_forecast_when_using_day_units(): void
    {
        $this->setActualTime();

        $this->createOrdersForPreviousNumberOfDays(30, [
            'value' => 1000.00,
        ]);

        $response = $this->signIn()
            ->getJson(route('revenue.index', [
                'report_date_from' => now()->toDateString(),
                'report_date_to' => now()->toDateString(),
            ]))
            ->json('totalRevenue.chart');

        $this->assertSame(
            [
                'labels' => [
                    '10/01',
                ],
                'datasets' => [
                    [
                        'label' => 'Total Revenue by Volume of Sales',
                        'data' => [
                            10,
                        ],
                    ],
                ],
            ],
            $response
        );
    }

    /** @test */
    public function it_gets_the_total_revenue_chart_for_a_seven_day_forecast_when_using_day_units(): void
    {
        $this->setActualTime();

        $this->createOrdersForPreviousNumberOfDays(30, [
            'value' => 1000.00,
        ]);

        $response = $this->signIn()
            ->getJson(route('revenue.index', [
                'report_date_from' => now()->subDays(29)->toDateString(),
            ]))
            ->json('totalRevenue.chart');

        $this->assertSame(
            [
                'labels' => [
                    '12/12',
                    '13/12',
                    '14/12',
                    '15/12',
                    '16/12',
                    '17/12',
                    '18/12',
                    '19/12',
                    '20/12',
                    '21/12',
                    '22/12',
                    '23/12',
                    '24/12',
                    '25/12',
                    '26/12',
                    '27/12',
                    '28/12',
                    '29/12',
                    '30/12',
                    '31/12',
                    '01/01',
                    '02/01',
                    '03/01',
                    '04/01',
                    '05/01',
                    '06/01',
                    '07/01',
                    '08/01',
                    '09/01',
                    '10/01',
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
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                            10,
                        ],
                    ],
                ],
            ],
            $response
        );
    }

    /** @test */
    public function it_gets_the_dual_dataset_for_comparison_on_total_revenue_when_using_day_units(): void
    {
        $this->createOrderForSubscription([
            'value' => 10000,
            'created_at' => Carbon::create(2021, 05, 05),
        ], 10);

        $this->createOrderForSubscription([
            'value' => 20000,
            'created_at' => Carbon::create(2021, 05, 15),
        ], 10);

        $response = $this->signIn()
            ->getJson(route('revenue.index', [
                'report_date_from' => Carbon::create(2021, 05, 01)->toDateString(),
                'report_date_to' => Carbon::create(2021, 05, 10)->toDateString(),
                'compare_date_from' => Carbon::create(2021, 05, 11)->toDateString(),
                'compare_date_to' => Carbon::create(2021, 05, 20)->toDateString(),
            ]))
            ->json('totalRevenue.chart.datasets');

        $this->assertSame(
            [
                [
                    'label' => 'Total Revenue by Volume of Sales',
                    'data' => [
                        0,
                        0,
                        0,
                        0,
                        1000,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label' => 'Compared To',
                    'data' => [
                        0,
                        0,
                        0,
                        0,
                        2000,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
            ],
            $response
        );
    }

    /** @test */
    public function by_default_it_doesnt_include_a_comparison_for_total_sales_revenue_when_using_day_units(): void
    {
        $response = $this->signIn()
            ->getJson(route('revenue.index'))
            ->json('totalRevenue.chart.datasets');

        $this->assertEquals(
            [
                [
                    "label" => "Total Revenue by Volume of Sales",
                    "data" => [0, 0, 0, 0, 0, 0, 0, 0],
                ],
            ],
            $response
        );
    }

    /**
     * @test
     * @dataProvider unsuccessfulReportComparisons
     */
    public function it_cant_add_a_comparison_if_the_length_of_the_period_is_different($payload): void
    {
        $this->signIn()->getJson(route('revenue.index', $payload))->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     * @dataProvider successfulReportComparisons
     */
    public function it_can_add_a_comparison_if_the_length_of_the_period_is_the_same($payload): void
    {
        $this->signIn()->getJson(route('revenue.index', $payload))->assertOk();
    }

    /**
     * @return array
     */
    public function successfulReportComparisons(): array
    {
        return [
            [
                [
                    'report_date_from' => Carbon::create(2021, 05, 01)->toDateString(),
                    'report_date_to' => Carbon::create(2021, 05, 10)->toDateString(),
                    'compare_date_from' => Carbon::create(2021, 05, 11)->toDateString(),
                    'compare_date_to' => Carbon::create(2021, 05, 20)->toDateString(),
                ],
            ],
            [
                [
                    'report_date_from' => Carbon::create(2021, 05, 01)->toDateString(),
                    'report_date_to' => Carbon::create(2021, 05, 10)->toDateString(),
                ],
            ],
            [
                [
                    'report_date_from' => null,
                    'report_date_to' => null,
                    'compare_date_from' => null,
                    'compare_date_to' => null,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function unsuccessfulReportComparisons(): array
    {
        return [
            [
                [
                    'report_date_from' => Carbon::create(2021, 05, 01)->toDateString(),
                    'report_date_to' => Carbon::create(2021, 05, 10)->toDateString(),
                    'compare_date_from' => Carbon::create(2021, 05, 11)->toDateString(),
                    'compare_date_to' => Carbon::create(2021, 05, 19)->toDateString(),
                ],
            ],
            [
                [
                    'report_date_from' => '2022-03-01T00:00:00.000Z',
                    'report_date_to' => '2022-03-06T00:00:00.000Z',
                    'compare_date_from' => '2022-01-01T00:00:00.000Z',
                    'compare_date_to' => '2022-03-07T00:00:00.000Z',
                ],
            ],
        ];
    }

    /** @test */
    public function it_gets_the_purchase_type_breakdown_revenue_with_all_empty_values_when_using_day_units(): void
    {
        $response = $this->signIn()
            ->getJson(route('revenue.index'))
            ->json('purchaseTypeBreakdown.chart');

        $this->assertSame(
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
            $response
        );
    }

    /** @test */
    public function it_gets_the_purchase_type_breakdown_revenue_for_a_single_type_of_order_when_using_day_units(): void
    {
        $this->createDifferentTypesOfOrders();

        $response = $this->signIn()
            ->getJson(route('revenue.index', [
                'report_date_from' => now()->subWeek()->toDateString(),
                'report_date_to' => now()->addDay()->toDateString(),
            ]))
            ->json('purchaseTypeBreakdown.chart');

        $this->assertSame(
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
                            25,
                            32,
                            99,
                            216,
                            0,
                            35,
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
            $response
        );
    }

    /** @test */
    public function it_gets_the_average_yield_for_a_single_customer_when_using_day_units(): void
    {
        $this->setActualTime(2022, 3, 7);

        $this->createOrdersForPreviousNumberOfDays(7, [
            'member_id' => $this->createAdministrator()->getKey(),
            'value' => 1000000,
        ]);

        $response = $this->signIn()
            ->getJson(route('revenue.index', [
                'report_date_from' => now()->subWeek()->toDateString(),
                'report_date_to' => now()->addDay()->toDateString(),
            ]))
            ->json('averageYield.chart');

        $this->assertSame(
            [
                'labels' => [
                    '01/03',
                ],
                'datasets' => [
                    [
                        'label' => 'Mean Average Charge per Guest',
                        'data' => ['10,000.00'],
                    ],
                ],
            ],
            $response
        );
    }

    /** @test */
    public function it_gets_the_all_subscription_revenue_chart_a_week_when_using_day_units(): void
    {
        $this->setActualTime(2022, 3, 7);

        $this->createOrdersForPreviousNumberOfDays(7, [
            'member_id' => $this->createAdministrator()->getKey(),
            'value' => 1000000,
        ]);

        $response = $this->signIn()
            ->getJson(route('revenue.index', [
                'report_date_from' => now()->subDays(6)->toDateString(),
                'report_date_to' => now()->toDateString(),
            ]))
            ->json('allSubscriptions.chart');

        $this->assertSame(
            [
                'labels' => [
                    '01/03',
                    '02/03',
                    '03/03',
                    '04/03',
                    '05/03',
                    '06/03',
                    '07/03',
                ],
                'datasets' => [
                    [
                        'label' => 'All Subscriptions Revenue',
                        'data' => [10000, 10000, 10000, 10000, 10000, 10000, 10000],
                    ],
                ],
            ],
            $response
        );
    }
}
