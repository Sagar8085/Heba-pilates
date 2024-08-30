<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class IntroPackCreditGuests
 *
 * @package App\Charts;
 */
class IntroPackCreditGuests extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->withIntroPackCredits());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.intro_pack_three_credit_guests');
    }
}