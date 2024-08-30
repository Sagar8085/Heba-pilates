<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class StandardMonthlySubscribers
 *
 * @package App\Charts;
 */
class StandardMonthlySubscribers extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withSubscriptionTiers([
            Subscription::STANDARD,
        ]));
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.premium_monthly_subscribers');
    }
}