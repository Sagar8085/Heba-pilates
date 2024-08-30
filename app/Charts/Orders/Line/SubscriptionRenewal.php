<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class SubscriptionRenewal
 *
 * @package App\Charts;
 */
class SubscriptionRenewal extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->recentlyRenewedSubscriptions());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.subscription_renewals');
    }
}