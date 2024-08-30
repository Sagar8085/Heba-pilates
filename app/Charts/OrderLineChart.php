<?php

namespace App\Charts;

use App\Charts\Orders\Line\TotalOrderRevenue;

/**
 * Class OrderLineChart
 *
 * @package App\Charts
 */
abstract class OrderLineChart extends LineChart
{
    abstract public function getLabel(): string;

    public function getReportDataset(): array
    {
        return [
            'label' => $this->getLabel(),
            'data' => $this->organiseOrdersByDates($this->getDates(), $this->getReportCollection())
                ->map->totalValue()
                ->roundAll()
                ->values()
                ->toArray(),
        ];
    }

    public function getComparisonDataset(): array
    {
        return [
            'label' => 'Compared To',
            'data' => $this->organiseOrdersByDates($this->getDates(), $this->getComparisonCollection())
                ->map->totalValue()
                ->roundAll()
                ->values()
                ->toArray(),
        ];
    }

    public function getMetrics(): array
    {
        return (new TotalOrderRevenue($this->getReportCollection(), $this->getComparisonCollection()))
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setCompareFrom($this->getCompareFrom())
            ->setCompareTo($this->getCompareTo())
            ->getDatasets();
    }
}