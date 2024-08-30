<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PremiumMonthlySubscribers
 *
 * @package App\Charts;
 */
class PremiumMonthlySubscribers extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withSubscriptionTiers([
            Subscription::PREMIUM_MEMBERSHIP_SUBSCRIPTION,
        ]));
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.premium_monthly_subscribers');
    }
}