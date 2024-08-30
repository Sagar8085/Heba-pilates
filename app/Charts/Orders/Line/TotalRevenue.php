<?php

namespace App\Charts\Orders\Line;

use App\Calculations\PercentageDifference;
use App\Charts\BasicChart;
use App\Collections\OrderCollection;
use Illuminate\Support\Collection;

/**
 * Class TotalRevenue
 *
 * @package App\Charts;
 */
class TotalRevenue extends BasicChart
{
    public function getReportDataset(): array
    {
        /** @var OrderCollection $orders */
        $orders = $this->getReportCollection();

        return collect([
            'value' => '£' . number_format($orders->totalValue(), 2) . ' (' . $orders->count() . ')',
        ])->pipe(function (Collection $collection) {

            if ($this->hasComparison()) {
                $calculator = (new PercentageDifference(
                    intval($this->getReportCollection()->totalValue() * 100),
                    intval($this->getComparisonCollection()->totalValue() * 100)
                ));

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
        /** @var OrderCollection $orders */
        $orders = $this->getComparisonCollection();

        return collect([
            'value' => '£' . number_format($orders->totalValue(), 2) . ' (' . $orders->count() . ')',
        ])->all();
    }
}