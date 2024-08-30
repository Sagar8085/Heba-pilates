<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\TotalSubscriptionRevenue;
use Tests\TestCase;

/**
 * Class TotalSubscriptionRevenueTest
 *
 * @package Tests\Charts
 */
class TotalSubscriptionRevenueTest extends TestCase
{
    /** @test */
    public function it_builds_the_chart_data_in_the_correct_format(): void
    {
        $orders = $this->createOrderForSubscription([
            'created_at' => now(),
            'value' => 100000,
        ], 15);

        $this->assertEquals(
            [
                'label' => 'All Subscriptions Revenue',
                'data' => [15000],
            ],
            (new TotalSubscriptionRevenue($orders))->getReportDataset()
        );
    }

    /** @test */
    public function it_builds_the_chart_data_for_a_week(): void
    {
        $orders = $this->createOrderForSubscription([
            'created_at' => now()->subWeek(),
            'value' => 100000,
        ], 15);

        $this->assertEquals(
            [
                'label' => 'All Subscriptions Revenue',
                'data' => [15000, 0, 0, 0, 0, 0, 0, 0],
            ],
            (new TotalSubscriptionRevenue($orders))
                ->setFrom(now()->subWeek())
                ->setTo(now())
                ->getReportDataset()
        );
    }

    /** @test */
    public function it_only_includes_subscriptions(): void
    {
        $orders = $this->createOrderForSubscription([
            'created_at' => now()->subWeek(),
            'value' => 100000,
        ], 15)
            ->merge(
                $this->createOrderForCreditPack([], 30)
            );

        $this->assertEquals(
            [
                'label' => 'All Subscriptions Revenue',
                'data' => [15000, 0, 0, 0, 0, 0, 0, 0],
            ],
            (new TotalSubscriptionRevenue($orders))
                ->setFrom(now()->subWeek())
                ->setTo(now())
                ->getReportDataset()
        );
    }

    /** @test */
    public function it_gets_subscriptions_all_week(): void
    {
        $orders = $this->createOrdersForPreviousNumberOfDays(8, [
            'value' => 4500,
        ])->merge(
            $this->createOrdersForPreviousNumberOfDays(8, [
                'value' => 3500,
            ])
        )->merge(
            $this->createOrdersForPreviousNumberOfDays(8, [
                'value' => 2500,
            ])
        );

        $this->assertEquals(
            [
                'label' => 'All Subscriptions Revenue',
                'data' => [105, 105, 105, 105, 105, 105, 105, 105],
            ],
            (new TotalSubscriptionRevenue($orders))
                ->setFrom(now()->subWeek())
                ->setTo(now())
                ->getReportDataset()
        );
    }
}
