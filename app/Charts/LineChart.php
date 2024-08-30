<?php

namespace App\Charts;

/**
 * Class LineChart
 *
 * @package App\Charts;
 */
abstract class LineChart extends OrderChart
{
    abstract public function getMetrics(): array;

    public function getLabels(): array
    {
        return $this->getDates()->map->format($this->getRangeHandler()->getFormat())->toArray();
    }
}