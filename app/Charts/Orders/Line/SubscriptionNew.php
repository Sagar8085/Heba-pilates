<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class SubscriptionNew
 *
 * @package App\Charts;
 */
class SubscriptionNew extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->notRecentlyRenewedSubscriptions());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.subscription_new');
    }
}