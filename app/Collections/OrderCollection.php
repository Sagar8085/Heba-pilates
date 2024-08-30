<?php

namespace App\Collections;

use App\Models\CreditPackPurchase;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection as BaseEloquentCollection;
use Illuminate\Support\Collection as BaseSupportCollection;

/**
 * Class OrderCollection
 *
 * @package App\Collections;
 */
class OrderCollection extends BaseEloquentCollection
{
    public function totalValue(): float
    {
        return $this->sum(fn (Order $order) => $order->value / 100);
    }

    public function withPromoCode(): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->promo_code);
    }

    public function withoutPromoCode(): OrderCollection
    {
        return $this->reject(fn (Order $order) => $order->promo_code);
    }

    private function filterByOrderableType(string $orderableType): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->orderable_type === $orderableType);
    }

    public function recentlyRenewedSubscriptions(): OrderCollection
    {
        return $this->filterByOrderableType(Subscription::class)
            ->filter(fn (Order $order) => optional($order->orderable)->renew == 1);
    }

    public function notRecentlyRenewedSubscriptions(): OrderCollection
    {
        return $this->filterByOrderableType(Subscription::class)
            ->filter(fn (Order $order) => optional($order->orderable)->renew == 0);
    }

    public function churnedSubscriptions(): OrderCollection
    {
        return $this->filterByOrderableType(Subscription::class)
            ->filter(function (Order $order) {
                //TODO: Add logic here to further filter by churn
                return $order;
            });
    }

    public function creditPacksOnly(): OrderCollection
    {
        return $this->filterByOrderableType(CreditPackPurchase::class);
    }

    public function subscriptionsOnly(): OrderCollection
    {
        return $this->filterByOrderableType(Subscription::class);
    }

    public function withSubscriptionTiers(array $tiers): OrderCollection
    {
        return $this->filter(fn (Order $order) => in_array(optional($order->orderable)->tier, $tiers));
    }

    public function withoutSubscriptionTiers(array $tiers): OrderCollection
    {
        return $this->filter(fn (Order $order) => !in_array($order->orderable->tier, $tiers));
    }

    public function getMeanOrderValues(): BaseSupportCollection
    {
        return $this->getMeanOrderAverages()
            ->map(fn (float $value) => number_format($value, 2))
            ->pipe(function (BaseSupportCollection $collection) {
                if ($collection->isEmpty()) {
                    return $collection->push(0);
                }

                return $collection;
            });
    }

    public function getMeanOrderAverages(): BaseSupportCollection
    {
        return $this->groupByMember()
            ->map->avg(fn (Order $order) => round($order->value / 100, 2))
            ->values();
    }

    public function getMeanOrderAverageDates(): array
    {
        return $this->groupByMember()
            ->map(function (OrderCollection $collection) {
                return $collection->sortBy('created_at')->first()->created_at;
            })
            ->sort()
            ->unique()
            ->map->format('d/m')
            ->values()
            ->toArray();
    }

    public function getAverageOfMeanOrderAverages(): float
    {
        $averages = $this->getMeanOrderAverages();

        return $averages->isNotEmpty()
            ? $averages->sum() / $averages->count()
            : 0;
    }

    private function groupByMember(): BaseEloquentCollection
    {
        return $this->groupBy(fn (Order $order) => $order->member_id);
    }

    public function withThirtyPackCredits(): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->isForThirtyPackCredits());
    }

    public function withTenPackCredits(): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->isForTenPackCredits());
    }

    public function withOnePackCredits(): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->isForOnePackCredits());
    }

    public function withIntroPackCredits(): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->isForIntroPackCredits());
    }

    public function renewalsOnly(): OrderCollection
    {
        return $this->filter(fn (Order $order) => $order->isARenewal());
    }

    public function nonRenewalsOnly(): OrderCollection
    {
        return $this->reject(fn (Order $order) => $order->isARenewal());
    }

    public function firstTimePurchasesOnly(): OrderCollection
    {
        return $this->reject(fn (Order $order) => $order->relatedOrders->isNotEmpty())
            ->nonRenewalsOnly();
    }
}