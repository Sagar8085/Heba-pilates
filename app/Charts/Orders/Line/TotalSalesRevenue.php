<?php

namespace App\Charts\Orders\Line;

use App\Charts\LineChart;
use App\Collections\OrderCollection;
use Illuminate\Support\Collection;

/**
 * Class TotalSalesRevenue
 *
 * @package App\Charts;
 */
class TotalSalesRevenue extends LineChart
{
    public function getReportDataset(): array
    {
        return [
            'label' => 'Total Revenue by Volume of Sales',
            'data' => $this->formatCollectionForChart(
                $this->organiseOrdersByDates($this->getDates(), $this->getReportCollection())
            ),
        ];
    }

    public function getComparisonDataset(): array
    {
        return [
            'label' => 'Compared To',
            'data' => $this->formatCollectionForChart(
                $this->organiseOrdersByDates($this->getComparisonDates(), $this->getComparisonCollection())
            ),
        ];
    }

    protected function formatCollectionForChart(Collection $collection): array
    {
        return $collection->toBase()
            ->map(fn (OrderCollection $collection) => $collection->totalValue())
            ->values()
            ->toArray();
    }

    public function getComparisonDates(): Collection
    {
        return collect(range(0, $this->getComparisonRange()))
            ->map(fn (int $day, $index) => $this->getComparisonStartDate()->addDays($index));
    }

    public function getMetrics(): array
    {
        return (new TotalRevenue($this->getReportCollection(), $this->getComparisonCollection()))
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setCompareFrom($this->getCompareFrom())
            ->setCompareTo($this->getCompareTo())
            ->getDatasets();
    }
}
