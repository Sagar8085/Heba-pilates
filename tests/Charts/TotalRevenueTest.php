<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\TotalRevenue;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class TotalRevenueTest
 *
 * @package Tests\Charts
 */
class TotalRevenueTest extends TestCase
{
    /** @test */
    public function it_builds_the_report_dataset_in_the_correct_format(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrdersForPreviousNumberOfDays(7, [
            'value' => 1000000,
        ]);

        $dataset = (new TotalRevenue($orders))->getReportDataset();

        $this->assertEquals(
            [
                'value' => '£70,000.00 (7)',
            ],
            $dataset
        );
    }

    /** @test */
    public function it_returns_zero_when_there_are_no_orders(): void
    {
        $dataset = (new TotalRevenue())->getReportDataset();

        $this->assertEquals(
            [
                'value' => '£0.00 (0)',
            ],
            $dataset
        );
    }

    /** @test */
    public function it_returns_the_totals_with_the_value_differences(): void
    {
        $earliest = Carbon::create(2021, 2);
        $latest = Carbon::create(2022, 2);

        $earlierOrders = $this->createOrderForSubscription([
            'value' => 25000,
            'created_at' => $earliest,
        ], 10);

        $latestOrders = $this->createOrderForSubscription([
            'value' => 50000,
            'created_at' => $latest,
        ], 15);

        $dataset = (new TotalRevenue($earlierOrders, $latestOrders))
            ->setFrom($earliest)
            ->setTo($earliest->copy()->addWeek())
            ->setCompareFrom($latest)
            ->setCompareTo($latest->copy()->addWeek())
            ->getDatasets();

        $this->assertEquals(
            [
                [
                    'value' => '£2,500.00 (10)',
                    'valueDiffPercentage' => '+200%',
                    'valueDiffType' => 'positive'
                ],
                [
                    'value' => '£7,500.00 (15)',
                ],
            ],
            $dataset
        );
    }
}
