<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\TotalSalesRevenue;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class ChartTest
 *
 * @package Tests\Charts;
 */
class ChartTest extends TestCase
{
    /** @test */
    public function it_assumes_the_start_date_as_the_earliest_of_the_orders(): void
    {
        $this->setActualTime(2022, 2, 8);

        $orders = $this->createOrdersForPreviousNumberOfDays(7);

        $this->assertEquals(
            Carbon::create(2022, 2, 2),
            (new TotalSalesRevenue($orders))->getStartDate()
        );
    }

    /** @test */
    public function it_defaults_the_start_date_as_seven_days_ago(): void
    {
        $this->setActualTime(2022, 2, 8);

        $this->assertEquals(
            Carbon::create(2022, 2, 1),
            (new TotalSalesRevenue)->getStartDate()
        );
    }

    /** @test */
    public function it_accepts_an_override_for_start_date(): void
    {
        $this->assertEquals(
            Carbon::create(1980, 2, 2),
            (new TotalSalesRevenue)
                ->setFrom(Carbon::create(1980, 2, 2))
                ->getStartDate()
        );
    }

    /** @test */
    public function it_defaults_the_end_date_as_now(): void
    {
        $this->setActualTime(2022, 2, 8);

        $this->assertEquals(
            Carbon::create(2022, 2, 8),
            (new TotalSalesRevenue)->getEndDate()
        );
    }

    /** @test */
    public function it_knows_if_it_has_something_to_compare(): void
    {
        $this->assertTrue(
            (new TotalSalesRevenue($this->createOrderForSubscription([], 10), $this->createOrderForSubscription([], 10)))
                ->setCompareFrom(now())
                ->setCompareTo(now())
                ->hasComparison()
        );
    }

    /** @test */
    public function it_knows_if_it_doesnt_have_something_to_compare(): void
    {
        $this->assertFalse(
            (new TotalSalesRevenue($this->createOrderForSubscription([], 10), $this->createOrderForSubscription([], 10)))
                ->setCompareFrom(null)
                ->setCompareTo(null)
                ->hasComparison()
        );
    }

    /** @test */
    public function it_has_multiple_datasets_when_it_has_a_comparison_report(): void
    {
        $orders = $this->createOrderForSubscription([
            'value' => 10000,
            'created_at' => Carbon::create(2021, 05, 05),
        ], 10);

        $comparisonOrders = $this->createOrderForSubscription([
            'value' => 20000,
            'created_at' => Carbon::create(2021, 05, 15),
        ], 10);

        $datasets = (new TotalSalesRevenue($orders, $comparisonOrders))
            ->setFrom(Carbon::create(2021, 05, 01))
            ->setTo(Carbon::create(2021, 05, 10))
            ->setCompareFrom(Carbon::create(2021, 05, 11))
            ->setCompareTo(Carbon::create(2021, 05, 20))
            ->getDatasets();

        $this->assertEquals(
            [
                [
                    'label' => 'Total Revenue by Volume of Sales',
                    'data' => [
                        0.0,
                        0.0,
                        0.0,
                        0.0,
                        1000.0,
                        0.0,
                        0.0,
                        0.0,
                        0.0,
                        0.0,
                    ],
                ],
                [
                    'label' => 'Compared To',
                    'data' => [
                        0.0,
                        0.0,
                        0.0,
                        0.0,
                        2000.0,
                        0.0,
                        0.0,
                        0.0,
                        0.0,
                        0.0,
                    ],
                ],
            ],
            $datasets
        );
    }
}