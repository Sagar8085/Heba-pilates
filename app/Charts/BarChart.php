<?php

namespace App\Charts;

/**
 * Class BarChart
 *
 * @package App\Charts;
 */
abstract class BarChart extends OrderChart
{
    abstract public function getLabels(): array;

    abstract public function getMetrics(): array;

    public function getReportDataset(): array
    {
        return $this->buildPayload($this->getReportCollection());
    }

    public function getComparisonDataset(): array
    {
        return $this->buildPayload($this->getComparisonCollection());
    }
}