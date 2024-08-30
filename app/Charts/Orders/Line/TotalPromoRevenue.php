<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TotalPromoRevenue
 *
 * @package App\Charts;
 */
class TotalPromoRevenue extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withPromoCode());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.promotion');
    }
}