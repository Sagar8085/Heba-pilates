<?php

namespace App\Charts\Orders\Bar;

use App\Charts\BarChart;
use App\Charts\Orders\Line\TotalOrderRevenue;
use App\Collections\OrderCollection;
use App\Constants\Subscriptions;
use App\Helpers\HexadecimalColorCode;
use App\Models\Subscription;

/**
 * Class PendingSubscriptionRevenue
 *
 * @package App\Charts;
 */
class PendingSubscriptionRevenue extends BarChart
{
    public function getLabels(): array
    {
        return Subscriptions::ALL;
    }

    public function getMetrics(): array
    {
        return (new TotalOrderRevenue($this->getReportCollection(), $this->getComparisonCollection()))
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setCompareFrom($this->getCompareFrom())
            ->setCompareTo($this->getCompareTo())
            ->getDatasets();
    }

    public function buildPayload(OrderCollection $orders): array
    {
        $orders = $this->getReportCollection();

        return [
            'data' => [
                $orders->withSubscriptionTiers([Subscription::VIP_UNLIMITED])->totalValue(),
                $orders->withSubscriptionTiers([Subscription::UNLIMITED_MEMBERSHIP_SUBSCRIPTION])->totalValue(),
                $orders->withSubscriptionTiers([Subscription::PREMIUM_MEMBERSHIP_SUBSCRIPTION])->totalValue(),
                $orders->withSubscriptionTiers([Subscription::STANDARD])->totalValue(),
            ],
            'backgroundColor' => collect(Subscriptions::ALL)
                ->map(fn (string $key) => HexadecimalColorCode::get($key))
                ->toArray(),
        ];
    }
}