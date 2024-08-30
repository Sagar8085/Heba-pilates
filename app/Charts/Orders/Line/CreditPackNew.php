<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CreditPackNew
 *
 * @package App\Charts;
 */
class CreditPackNew extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->creditPacksOnly()->firstTimePurchasesOnly());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.credit_pack_new');
    }
}
