<?php

namespace App\Traits;

use App\Charts\Orders\Line\TotalChurnRevenue;
use App\Collections\OrderCollection;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

trait CalculatesChurn
{
    abstract public function getReportCollection(): EloquentCollection;

    abstract public function getComparisonCollection(): EloquentCollection;

    public function organiseOrdersByDates(SupportCollection $dates, OrderCollection $orders): SupportCollection
    {
        $factor = Str::of($this->getUnit())->lower()->ucfirst();

        return $dates->mapWithKeys(fn (Carbon $date) => [
            $date->format('d/m/Y') => Order::query()
                ->whereBetween('expires', [
                    $date->copy()->{'startOf' . $factor}()->subWeek(),
                    $date->copy()->{'endOf' . $factor}()->subWeek(),
                ])
                ->get(),
        ]);
    }

    public function getMetrics(): array
    {
        return (new TotalChurnRevenue($this->getReportCollection(), $this->getComparisonCollection()))
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setCompareFrom($this->getCompareFrom())
            ->setCompareTo($this->getCompareTo())
            ->getDatasets();
    }
}