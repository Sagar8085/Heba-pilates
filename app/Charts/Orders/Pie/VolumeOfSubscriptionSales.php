<?php

namespace App\Charts\Orders\Pie;

use App\Charts\OrderPieChart;
use App\Collections\OrderCollection;
use App\Constants\Subscriptions;
use App\Helpers\HexadecimalColorCode;
use App\Models\Subscription;

/**
 * Class VolumeOfSubscriptionSales
 *
 * @package App\Charts;
 */
class VolumeOfSubscriptionSales extends OrderPieChart
{
    public function getLabels(): array
    {
        return Subscriptions::ALL;
    }

    public function buildPayload(OrderCollection $orders): array
    {
        return [
            'data' => [
                $orders->withSubscriptionTiers([Subscription::VIP_UNLIMITED])->count(),
                $orders->withSubscriptionTiers([Subscription::UNLIMITED_MEMBERSHIP_SUBSCRIPTION])->count(),
                $orders->withSubscriptionTiers([Subscription::PREMIUM_MEMBERSHIP_SUBSCRIPTION])->count(),
                $orders->withSubscriptionTiers([Subscription::STANDARD])->count(),
            ],
            'backgroundColor' => collect(Subscriptions::ALL)
                ->map(fn (string $key) => HexadecimalColorCode::get($key))
                ->toArray(),
        ];
    }

    public function getLinks(): array
    {
        return collect($this->getLabels())
            ->map(function (string $purchaseType, int $index) {
                return [
                    'index' => $index,
                    'type' => $purchaseType,
                    'link' => '/admin/members?type=' . $purchaseType,
                ];
            })
            ->toArray();
    }
}