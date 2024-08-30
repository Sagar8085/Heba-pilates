<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OnePackCreditGuests
 *
 * @package App\Charts;
 */
class OnePackCreditGuests extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withOnePackCredits());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.one_pack_credit_guests');
    }
}