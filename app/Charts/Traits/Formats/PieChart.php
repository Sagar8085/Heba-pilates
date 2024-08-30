<?php

namespace App\Charts\Traits\Formats;

/**
 * Trait NeedsPieChartFormat
 *
 * @package App\Traits;
 */
trait PieChart
{
    abstract public function getLabels(): array;

    abstract public function getDatasets(): array;

    abstract public function getLinks(): array;

    public function toChart(): array
    {
        return [
            'chart' => [
                'labels' => $this->getLabels(),
                'datasets' => $this->getDatasets(),
            ],
            'links' => $this->getLinks(),
        ];
    }
}