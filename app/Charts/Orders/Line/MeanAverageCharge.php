<?php

namespace App\Charts\Orders\Line;

use App\Calculations\PercentageDifference;
use App\Charts\BasicChart;
use Illuminate\Support\Collection;

/**
 * Class MeanAverageCharge
 *
 * @package App\Charts;
 */
class MeanAverageCharge extends BasicChart
{
    public function getReportDataset(): array
    {
        $orders = $this->getReportCollection();
        $comparisonOrders = $this->getComparisonCollection();

        return collect([
            'value' => '£' . number_format($orders->getAverageOfMeanOrderAverages(), 2) . ' (' . $orders->count() . ')',
        ])->pipe(function (Collection $collection) use ($comparisonOrders, $orders) {

            if ($this->hasComparison()) {
                $calculator = (new PercentageDifference(
                    intval($orders->getAverageOfMeanOrderAverages()),
                    intval($comparisonOrders->getAverageOfMeanOrderAverages())
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
        $orders = $this->getComparisonCollection();

        return collect([
            'value' => '£' . number_format($orders->getAverageOfMeanOrderAverages(), 2) . ' (' . $orders->count() . ')',
        ])->all();
    }
}