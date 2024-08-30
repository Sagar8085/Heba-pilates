<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\AverageYield;
use App\Collections\OrderCollection;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class AverageYieldTest
 *
 * @package Tests\Charts
 */
class AverageYieldTest extends TestCase
{
    /** @test */
    public function it_builds_the_report_dataset_in_the_correct_format(): void
    {
        $orders = $this->createOrdersForPreviousNumberOfDays(7, [
            'member_id' => $this->createAdministrator()->getKey(),
            'value' => 1000000,
        ]);

        $dataset = (new AverageYield($orders))->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Mean Average Charge per Guest',
                'data' => ['10,000.00'],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_the_comparison_dataset_in_the_correct_format(): void
    {
        $orders = $this->createOrdersForPreviousNumberOfDays(7, [
            'member_id' => $this->createAdministrator()->getKey(),
            'value' => 1000000,
        ]);

        $dataset = (new AverageYield($orders, $orders))->getComparisonDataset();

        $this->assertEquals(
            [
                'label' => 'Compared To',
                'data' => ['10,000.00'],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_the_report_dataset_for_the_number_of_customers_per_range(): void
    {
        $orders = collect(range(1, 7))
            ->map(fn () => $this->createOrdersForPreviousNumberOfDays(7, [
                'member_id' => $this->createAdministrator()->getKey(),
                'value' => 1000000,
            ]))
            ->flatten()
            ->pipe(function (Collection $collection) {
                return OrderCollection::make($collection->values());
            });

        $dataset = (new AverageYield($orders))->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Mean Average Charge per Guest',
                'data' => ['10,000.00', '10,000.00', '10,000.00', '10,000.00', '10,000.00', '10,000.00', '10,000.00'],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_the_comparison_data_for_the_number_of_customers_per_range(): void
    {
        $orders = collect(range(1, 7))
            ->map(fn () => $this->createOrdersForPreviousNumberOfDays(7, [
                'member_id' => $this->createAdministrator()->getKey(),
                'value' => 1000000,
            ]))
            ->flatten()
            ->pipe(function (Collection $collection) {
                return OrderCollection::make($collection->values());
            });

        $dataset = (new AverageYield($orders))->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Mean Average Charge per Guest',
                'data' => ['10,000.00', '10,000.00', '10,000.00', '10,000.00', '10,000.00', '10,000.00', '10,000.00'],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_an_empty_dataset_in_the_correct_format(): void
    {
        $dataset = (new AverageYield)->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Mean Average Charge per Guest',
                'data' => [0],
            ],
            $dataset
        );
    }
}
