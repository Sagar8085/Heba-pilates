<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use App\Traits\CalculatesChurn;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CreditPackChurn
 *
 * @package App\Charts;
 */
class CreditPackChurn extends OrderLineChart
{
    use CalculatesChurn;

    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->creditPacksOnly());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.credit_pack_churn');
    }
}