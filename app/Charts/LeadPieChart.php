<?php

namespace App\Charts;

use App\Charts\Traits\Formats\PieChart;
use App\Collections\LeadCollection;

/**
 * Class PieChart
 *
 * @package App\Charts;
 */
abstract class LeadPieChart extends Chart
{
    use PieChart;

    abstract public function buildPayload(LeadCollection $leads): array;

    public function getReportDataset(): array
    {
        return $this->buildPayload($this->getReportCollection());
    }

    public function getComparisonDataset(): array
    {
        return $this->buildPayload($this->getComparisonCollection());
    }
}