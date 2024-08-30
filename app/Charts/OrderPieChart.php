<?php

namespace App\Charts;

use App\Charts\Traits\Formats\PieChart;
use App\Collections\OrderCollection;

/**
 * Class PieChart
 *
 * @package App\Charts;
 */
abstract class OrderPieChart extends OrderChart
{
    use PieChart;

    abstract public function buildPayload(OrderCollection $orders): array;

    public function getReportDataset(): array
    {
        return $this->buildPayload($this->getReportCollection());
    }

    public function getComparisonDataset(): array
    {
        return $this->buildPayload($this->getComparisonCollection());
    }
}