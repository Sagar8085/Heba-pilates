<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ThirtyPackCreditGuests
 *
 * @package App\Charts;
 */
class ThirtyPackCreditGuests extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withThirtyPackCredits());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.thirty_pack_credit_guests');
    }
}