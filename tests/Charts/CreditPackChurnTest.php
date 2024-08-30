<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\CreditPackChurn;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class CreditPackChurnTest
 *
 * @package Tests\Charts;
 */
class CreditPackChurnTest extends TestCase
{
    /** @test */
    public function it_returns_the_count_totals(): void
    {
        $creditPack = $this->createCreditPack(null, [
            'promotional' => false,
        ]);

        $this->createOrderForCreditPack([
            'value' => 10000,
            'expires' => Carbon::create(2021, 1, 3),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 20000,
            'expires' => Carbon::create(2021, 1, 4),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 30000,
            'expires' => Carbon::create(2021, 1, 5),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 40000,
            'expires' => Carbon::create(2021, 1, 6),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 50000,
            'expires' => Carbon::create(2021, 1, 7),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 60000,
            'expires' => Carbon::create(2021, 1, 8),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 70000,
            'expires' => Carbon::create(2021, 1, 9),
        ], 1, $creditPack);

        $this->createOrderForCreditPack([
            'value' => 80000,
            'expires' => Carbon::create(2021, 1, 10),
        ], 1, $creditPack);

        $orders = Order::query()
            ->where([
                ['expires', '>', Carbon::create(2021, 1, 10)]
            ])
            ->get();

        $dataset = (new CreditPackChurn($orders))
            ->setFrom(
                Carbon::create(2021, 1, 10)
            )
            ->setTo(
                Carbon::create(2021, 1, 17)
            )
            ->toChart();

        $this->assertEquals(
            [
                'chart' => [
                    'labels' => [
                        '10/01',
                        '11/01',
                        '12/01',
                        '13/01',
                        '14/01',
                        '15/01',
                        '16/01',
                        '17/01',
                    ],
                    'datasets' => [
                        0 => [
                            'label' => 'Credit Pack Churn',
                            'data' => [
                                100.0,
                                200.0,
                                300.0,
                                400.0,
                                500.0,
                                600.0,
                                700.0,
                                800.0,
                            ],
                        ],
                    ],
                ],
                'metrics' => [
                    0 => [
                        'value' => '£3,600.00 (8)',
                    ],
                ],
            ],
            $dataset
        );
    }
}