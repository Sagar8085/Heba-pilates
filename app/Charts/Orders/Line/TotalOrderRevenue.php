<?php

namespace App\Charts\Orders\Line;

use App\Calculations\PercentageDifference;
use App\Charts\BasicChart;
use App\Collections\OrderCollection;
use Illuminate\Support\Collection;

/**
 * Class AllSubscriptionRevenue
 *
 * @package App\Charts;
 */
class TotalOrderRevenue extends BasicChart
{
    public function getReportDataset(): array
    {
        $orderCollections = $this->organiseOrdersByDates($this->getDates(), $this->getReportCollection());
        $comparisonOrderCollections = $this->organiseOrdersByDates($this->getDates(), $this->getComparisonCollection());

        $orderTotal = $orderCollections->map->totalValue()->sum();
        $comparisonOrderTotal = $comparisonOrderCollections->map->totalValue()->sum();

        $count = $orderCollections->sum(fn (OrderCollection $collection) => $collection->count());

        return collect([
            'value' => '£' . number_format($orderTotal, 2) . ' (' . $count . ')',
        ])->pipe(function (Collection $collection) use ($comparisonOrderTotal, $orderTotal) {

            if ($this->hasComparison()) {
                $calculator = (new PercentageDifference(intval($orderTotal), intval($comparisonOrderTotal)));

                return $collection->merge([
                    'valueDiffPercentage' => $calculator->calculate(),
                    'valueDiffType' => $calculator->getCalculationResult(),
                ]);
            }

            return $collection;
        })->all();
    }

    public function getComparisonDataset(): array
    {
        $comparisonOrderCollections = $this->organiseOrdersByDates($this->getDates(), $this->getComparisonCollection());
        $comparisonOrderTotal = $comparisonOrderCollections->map->totalValue()->sum();

        $count = $comparisonOrderCollections->sum(fn (OrderCollection $collection) => $collection->count());

        return collect([
            'value' => '£' . number_format($comparisonOrderTotal, 2) . ' (' . $count . ')',
        ])->all();
    }
}