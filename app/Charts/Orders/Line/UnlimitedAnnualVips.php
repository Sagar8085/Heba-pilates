<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UnlimitedAnnualVips
 *
 * @package App\Charts;
 */
class UnlimitedAnnualVips extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withSubscriptionTiers([
            Subscription::VIP_UNLIMITED,
        ]));
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.unlimited_annual_vips');
    }
}