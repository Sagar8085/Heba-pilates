<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\TotalCreditPackRevenue;
use Tests\TestCase;

class TotalCreditPackRevenueTest extends TestCase
{

    /** @test */
    public function it_builds_the_chart_data_in_the_correct_format(): void
    {
        $orders = $this->createOrderForCreditPack([
            'created_at' => now(),
            'value' => 100000,
        ], 15);

        $this->assertEquals(
            [
                'label' => 'All Credit Pack Revenue',
                'data' => [15000],
            ],
            (new TotalCreditPackRevenue($orders))->getReportDataset()
        );
    }

    /** @test */
    public function it_builds_the_chart_data_for_a_week(): void
    {
        $orders = $this->createOrderForCreditPack([
            'created_at' => now()->subWeek(),
            'value' => 100000,
        ], 15);

        $this->assertEquals(
            [
                'label' => 'All Credit Pack Revenue',
                'data' => [15000, 0, 0, 0, 0, 0, 0, 0],
            ],
            (new TotalCreditPackRevenue($orders))
                ->setFrom(now()->subWeek())
                ->setTo(now())
                ->getReportDataset()
        );
    }

    /** @test */
    public function it_only_includes_credit_packs(): void
    {
        $orders = $this->createOrderForCreditPack([
            'created_at' => now()->subWeek(),
            'value' => 100000,
        ], 15)
            ->merge(
                $this->createOrderForSubscription([], 30)
            );

        $this->assertEquals(
            [
                'label' => 'All Credit Pack Revenue',
                'data' => [15000, 0, 0, 0, 0, 0, 0, 0],
            ],
            (new TotalCreditPackRevenue($orders))
                ->setFrom(now()->subWeek())
                ->setTo(now())
                ->getReportDataset()
        );
    }

    /** @test */
    public function it_gets_subscriptions_all_week(): void
    {
        $orders = $this->createOrderForCreditPack([
            'value' => 2500,
            'created_at' => now()->subWeek(),
        ], 8)
            ->merge(
                $this->createOrderForCreditPack([
                    'value' => 2500,
                    'created_at' => now()->subDays(6),
                ], 8)
            )->merge(
                $this->createOrderForCreditPack([
                    'value' => 3500,
                    'created_at' => now()->subDays(5),
                ], 8)
            )->merge(
                $this->createOrderForCreditPack([
                    'value' => 2500,
                    'created_at' => now()->subDays(4),
                ], 8)
            )->merge(
                $this->createOrderForCreditPack([
                    'value' => 2500,
                    'created_at' => now()->subDays(3),
                ], 8)
            )->merge(
                $this->createOrderForCreditPack([
                    'value' => 2500,
                    'created_at' => now()->subDays(2),
                ], 8)
            )->merge(
                $this->createOrderForCreditPack([
                    'value' => 2500,
                    'created_at' => now()->subDays(1),
                ], 8)
            )->merge(
                $this->createOrderForCreditPack([
                    'value' => 2500,
                    'created_at' => now(),
                ], 8)
            );

        $this->assertEquals(
            [
                'label' => 'All Credit Pack Revenue',
                'data' => [
                    200.0,
                    200.0,
                    280.0,
                    200.0,
                    200.0,
                    200.0,
                    200.0,
                    200.0,
                ],
            ],
            (new TotalCreditPackRevenue($orders))
                ->setFrom(now()->subWeek())
                ->setTo(now())
                ->getReportDataset()
        );
    }
}
