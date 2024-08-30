<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\MeanAverageCharge;
use Carbon\Carbon;
use Tests\TestCase;

class MeanAverageChargeTest extends TestCase
{
    /** @test */
    public function it_builds_the_report_dataset_in_the_correct_format(): void
    {
        $orders = $this->createOrdersForPreviousNumberOfDays(7, [
            'member_id' => $this->createAdministrator()->getKey(),
            'value' => 2500,
        ])->merge(
            $this->createOrdersForPreviousNumberOfDays(7, [
                'member_id' => $this->createAdministrator()->getKey(),
                'value' => 2800,
            ])
        )->merge(
            $this->createOrdersForPreviousNumberOfDays(7, [
                'member_id' => $this->createAdministrator()->getKey(),
                'value' => 3500,
            ])
        )->merge(
            $this->createOrdersForPreviousNumberOfDays(7, [
                'member_id' => $this->createAdministrator()->getKey(),
                'value' => 2600,
            ])
        )->merge(
            $this->createOrdersForPreviousNumberOfDays(7, [
                'member_id' => $this->createAdministrator()->getKey(),
                'value' => 3500,
            ])
        );

        $dataset = (new MeanAverageCharge($orders))->getReportDataset();

        $this->assertEquals(
            [
                'value' => '£29.80 (35)',
            ],
            $dataset
        );
    }

    /** @test */
    public function it_returns_zero_when_there_are_no_orders(): void
    {
        $dataset = (new MeanAverageCharge)->getReportDataset();

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

        $dataset = (new MeanAverageCharge($earlierOrders, $latestOrders))
            ->setFrom($earliest->copy()->subDay())
            ->setTo($earliest->copy()->addWeek())
            ->setCompareFrom($latest->copy()->subDay())
            ->setCompareTo($latest->copy()->addWeek())
            ->getDatasets();

        $this->assertEquals(
            [
                [
                    'value' => '£250.00 (10)',
                    'valueDiffPercentage' => '+100%',
                    'valueDiffType' => 'positive'
                ],
                [
                    'value' => '£500.00 (15)',
                ],
            ],
            $dataset
        );
    }
}
