<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TenPackCreditGuests
 *
 * @package App\Charts;
 */
class TenPackCreditGuests extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withTenPackCredits());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.ten_pack_credit_guests');
    }
}