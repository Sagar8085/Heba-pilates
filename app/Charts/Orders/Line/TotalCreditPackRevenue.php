<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TotalCreditPackRevenue
 *
 * @package App\Charts;
 */
class TotalCreditPackRevenue extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->creditPacksOnly());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.credit_pack');
    }
}