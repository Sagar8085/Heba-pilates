<?php

namespace Tests\Charts;

use App\Charts\Orders\Line\TotalSalesRevenue;
use App\Collections\OrderCollection;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class TotalSalesRevenueTest
 *
 * @package Tests\Chart
 */
class TotalSalesRevenueTest extends TestCase
{
    /** @test */
    public function it_builds_the_chart_data_in_the_correct_format(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 4),
            'value' => 1000,
        ], 1);

        $dataset = (new TotalSalesRevenue($orders))->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Total Revenue by Volume of Sales',
                'data' => [10, 0, 0, 0, 0, 0, 0],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_an_ascending_list_of_report_dates_for_seven_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrdersForPreviousNumberOfDays(7);

        $dates = (new TotalSalesRevenue($orders))->getDates();

        $this->assertEquals(
            collect([
                Carbon::create(2022, 2, 4),
                Carbon::create(2022, 2, 5),
                Carbon::create(2022, 2, 6),
                Carbon::create(2022, 2, 7),
                Carbon::create(2022, 2, 8),
                Carbon::create(2022, 2, 9),
                Carbon::create(2022, 2, 10),
            ]),
            $dates
        );
    }

    /** @test */
    public function it_builds_the_labels_of_report_dates_for_seven_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrdersForPreviousNumberOfDays(7);

        $labels = (new TotalSalesRevenue($orders))->getLabels();

        $this->assertEquals(
            [
                '04/02',
                '05/02',
                '06/02',
                '07/02',
                '08/02',
                '09/02',
                '10/02',
            ],
            $labels
        );
    }

    /** @test */
    public function it_builds_a_collection_of_order_collections_by_date_for_seven_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 4),
        ], 1);

        $revenue = (new TotalSalesRevenue($orders));

        $collection = $revenue->organiseOrdersByDates($revenue->getDates(), $orders);

        $this->assertEquals(
            collect([
                '04/02/2022' => $orders,
                '05/02/2022' => new OrderCollection,
                '06/02/2022' => new OrderCollection,
                '07/02/2022' => new OrderCollection,
                '08/02/2022' => new OrderCollection,
                '09/02/2022' => new OrderCollection,
                '10/02/2022' => new OrderCollection,
            ]),
            $collection
        );
    }

    /** @test */
    public function it_builds_a_report_dataset_for_many_orders_a_week_for_seven_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 2);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 4),
            'value' => 9000,
        ], 10);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 5),
            'value' => 9000,
        ], 9);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 6),
            'value' => 9000,
        ], 8);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 7),
            'value' => 9000,
        ], 7);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 8),
            'value' => 9000,
        ], 6);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 9),
            'value' => 9000,
        ], 5);

        $this->createOrderForSubscription([
            'created_at' => Carbon::create(2022, 2, 10),
            'value' => 9000,
        ], 4);

        $dataset = (new TotalSalesRevenue(Order::all()))->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Total Revenue by Volume of Sales',
                'data' => [
                    900,
                    810,
                    720,
                    630,
                    540,
                    450,
                    360,
                ],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_a_report_dataset_for_one_order_each_day_for_seven_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrdersForPreviousNumberOfDays(7, [
            'value' => 1000,
        ]);

        $dataset = (new TotalSalesRevenue($orders))->getReportDataset();

        $this->assertEquals(
            [
                'label' => 'Total Revenue by Volume of Sales',
                'data' => [10, 10, 10, 10, 10, 10, 10],
            ],
            $dataset
        );
    }

    /** @test */
    public function it_builds_an_ascending_list_of_dates_for_thirty_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 3, 31);

        $orders = $this->createOrdersForPreviousNumberOfDays(30);

        $startDate = Carbon::create(2022, 3);

        $dates = (new TotalSalesRevenue($orders))
            ->setFrom($startDate)
            ->getDates();

        $this->assertEquals(
            collect([
                $startDate,
                Carbon::create(2022, 3, 2),
                Carbon::create(2022, 3, 3),
                Carbon::create(2022, 3, 4),
                Carbon::create(2022, 3, 5),
                Carbon::create(2022, 3, 6),
                Carbon::create(2022, 3, 7),
                Carbon::create(2022, 3, 8),
                Carbon::create(2022, 3, 9),
                Carbon::create(2022, 3, 10),
                Carbon::create(2022, 3, 11),
                Carbon::create(2022, 3, 12),
                Carbon::create(2022, 3, 13),
                Carbon::create(2022, 3, 14),
                Carbon::create(2022, 3, 15),
                Carbon::create(2022, 3, 16),
                Carbon::create(2022, 3, 17),
                Carbon::create(2022, 3, 18),
                Carbon::create(2022, 3, 19),
                Carbon::create(2022, 3, 20),
                Carbon::create(2022, 3, 21),
                Carbon::create(2022, 3, 22),
                Carbon::create(2022, 3, 23),
                Carbon::create(2022, 3, 24),
                Carbon::create(2022, 3, 25),
                Carbon::create(2022, 3, 26),
                Carbon::create(2022, 3, 27),
                Carbon::create(2022, 3, 28),
                Carbon::create(2022, 3, 29),
                Carbon::create(2022, 3, 30),
                Carbon::create(2022, 3, 31),
            ]),
            $dates
        );
    }

    /** @test */
    public function it_builds_the_labels_of_dates_for_thirty_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 3, 31);

        $orders = $this->createOrdersForPreviousNumberOfDays(30);

        $labels = (new TotalSalesRevenue($orders))->getLabels();

        $this->assertEquals(
            [
                '02/03',
                '03/03',
                '04/03',
                '05/03',
                '06/03',
                '07/03',
                '08/03',
                '09/03',
                '10/03',
                '11/03',
                '12/03',
                '13/03',
                '14/03',
                '15/03',
                '16/03',
                '17/03',
                '18/03',
                '19/03',
                '20/03',
                '21/03',
                '22/03',
                '23/03',
                '24/03',
                '25/03',
                '26/03',
                '27/03',
                '28/03',
                '29/03',
                '30/03',
                '31/03',
            ],
            $labels
        );
    }

    /** @test */
    public function it_builds_an_ascending_list_of_comparison_dates_for_seven_days_when_using_day_units(): void
    {
        $this->setActualTime(2022, 2);

        $orders = $this->createOrdersForPreviousNumberOfDays(7);

        $dates = (new TotalSalesRevenue($orders))
            ->setCompareFrom(Carbon::create(2022, 2, 4))
            ->setCompareTo(Carbon::create(2022, 2, 10))
            ->getComparisonDates();

        $this->assertEquals(
            collect([
                Carbon::create(2022, 2, 4),
                Carbon::create(2022, 2, 5),
                Carbon::create(2022, 2, 6),
                Carbon::create(2022, 2, 7),
                Carbon::create(2022, 2, 8),
                Carbon::create(2022, 2, 9),
                Carbon::create(2022, 2, 10),
            ]),
            $dates
        );
    }
}
