<?php

namespace App\Charts;

/**
 * Class BasicChart
 *
 * @package App\Charts;
 */
abstract class BasicChart extends OrderChart
{
    public function toChart(): array
    {
        return [
            'chart' => $this->getDatasets(),
        ];
    }
}