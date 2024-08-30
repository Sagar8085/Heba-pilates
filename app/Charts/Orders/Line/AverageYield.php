<?php

namespace App\Charts\Orders\Line;

use App\Charts\LineChart;

/**
 * Class AverageYield
 *
 * @package App\Charts;
 */
class AverageYield extends LineChart
{
    public function getLabels(): array
    {
        return $this->getReportCollection()->getMeanOrderAverageDates();
    }

    public function getReportDataset(): array
    {
        return [
            'label' => 'Mean Average Charge per Guest',
            'data' => $this->getReportCollection()->getMeanOrderValues()->toArray(),
        ];
    }

    public function getComparisonDataset(): array
    {
        return [
            'label' => 'Compared To',
            'data' => $this->getComparisonCollection()->getMeanOrderValues()->toArray(),
        ];
    }

    public function getMetrics(): array
    {
        return (new MeanAverageCharge($this->getReportCollection(), $this->getComparisonCollection()))
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setCompareFrom($this->getCompareFrom())
            ->setCompareTo($this->getCompareTo())
            ->getDatasets();
    }

}