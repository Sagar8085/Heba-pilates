<?php

namespace App\Charts\Orders\Line;

use App\Charts\OrderLineChart;
use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CreditPackRenewal
 *
 * @package App\Charts;
 */
class CreditPackRenewal extends OrderLineChart
{
    public function applyCollectionScopes(Collection $reportCollection): Collection
    {
        /** @var OrderCollection $reportCollection */
        return parent::applyCollectionScopes($reportCollection->creditPacksOnly());
    }

    public function getLabel(): string
    {
        return __('charts.labels.revenue.credit_pack_renewals');
    }

    public function getReportDataset(): array
    {
        return [
            'label' => $this->getLabel(),
            'data' => $this->organiseOrdersByDates($this->getDates(), $this->getReportCollection())
                ->map->renewalsOnly()
                ->map->totalValue()
                ->roundAll()
                ->values()
                ->toArray(),
        ];
    }

    public function getComparisonDataset(): array
    {
        return [
            'label' => 'Compared To',
            'data' => $this->organiseOrdersByDates($this->getDates(), $this->getComparisonCollection())
                ->map->renewalsOnly()
                ->map->totalValue()
                ->roundAll()
                ->values()
                ->toArray(),
        ];
    }
}