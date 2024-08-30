<?php

namespace App\Charts;

use App\Collections\OrderCollection;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

/**
 * Class OrderChart
 *
 * @package App\Charts;
 */
abstract class OrderChart extends Chart
{
    public function __construct(EloquentCollection $orders = null, EloquentCollection $comparisonOrders = null)
    {
        parent::__construct($orders ?: new OrderCollection, $comparisonOrders ?: new OrderCollection);
    }

    public function organiseOrdersByDates(SupportCollection $dates, OrderCollection $orders): SupportCollection
    {
        $factor = Str::of($this->getUnit())->lower()->ucfirst();

        return $dates->mapWithKeys(fn (Carbon $date) => [
            $date->format('d/m/Y') => $orders->filter(function (Order $order) use ($factor, $date) {
                return $order->created_at->gte($date->{'startOf' . $factor}())
                    && $order->created_at->lte($date->{'endOf' . $factor}());
            }),
        ]);
    }
}