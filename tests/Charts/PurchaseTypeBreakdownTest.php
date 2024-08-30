<?php

namespace Tests\Charts;

use App\Charts\Orders\Pie\PurchaseTypeBreakdown;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class PurchaseTypeBreakdownTest
 *
 * @package Tests\Charts
 */
class PurchaseTypeBreakdownTest extends TestCase
{
    /** @test */
    public function it_returns_the_count_totals_grouped_by_order_type(): void
    {
        $this->createDifferentTypesOfOrders();

        $dataset = (new PurchaseTypeBreakdown(Order::all()))
            ->setFrom(
                Carbon::create(2021, 1, 31)
            )
            ->setTo(
                Carbon::create(2021, 2, 06)
            )
            ->toChart();

        $this->assertEquals(
            [
                'chart' => [
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
                            'data' => [25, 32, 99, 216, 0, 35, 0],
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
                'links' => [
                    [
                        'index' => 0,
                        'type' => 'Promo Purchases',
                        'link' => '/admin/members?type=Promo Purchases',
                    ],
                    [
                        'index' => 1,
                        'type' => 'Subscriptions Purchases',
                        'link' => '/admin/members?type=Subscriptions Purchases',
                    ],
                    [
                        'index' => 2,
                        'type' => 'New Subscriptions Purchases',
                        'link' => '/admin/members?type=New Subscriptions Purchases',
                    ],
                    [
                        'index' => 3,
                        'type' => 'Credit Pack Purchases',
                        'link' => '/admin/members?type=Credit Pack Purchases',
                    ],
                    [
                        'index' => 4,
                        'type' => 'Free Credits/Packs/Plans',
                        'link' => '/admin/members?type=Free Credits/Packs/Plans',
                    ],
                    [
                        'index' => 5,
                        'type' => 'VIP/Partner Credit Plans',
                        'link' => '/admin/members?type=VIP/Partner Credit Plans',
                    ],
                    [
                        'index' => 6,
                        'type' => 'Corporate Credit Plans',
                        'link' => '/admin/members?type=Corporate Credit Plans',
                    ],
                ],
            ],
            $dataset
        );
    }

}
