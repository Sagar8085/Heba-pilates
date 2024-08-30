<?php

namespace App\Charts\Orders\Pie;

use App\Charts\OrderPieChart;
use App\Collections\OrderCollection;
use App\Constants\PurchaseTypes;
use App\Helpers\HexadecimalColorCode;
use App\Models\Subscription;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class PurchaseTypeBreakdown
 *
 * @package App\Charts;
 */
class PurchaseTypeBreakdown extends OrderPieChart
{
    public function getLabels(): array
    {
        return PurchaseTypes::ALL;
    }

    #[ArrayShape(['data' => "array", 'backgroundColor' => "array"])]
    public function buildPayload(OrderCollection $orders): array
    {
        return [
            'data' => [
                $orders->withPromoCode()->count(),
                $orders->withoutPromoCode()->recentlyRenewedSubscriptions()->withoutSubscriptionTiers([
                    Subscription::VIP_UNLIMITED,
                ])->count(),
                $orders->withoutPromoCode()->notRecentlyRenewedSubscriptions()->count(),
                $orders->creditPacksOnly()->count(),
                0,
                //TODO: Free Credits/Packs/Plans - Confirm the criteria for an order that has free credits/packs plans
                $orders->withoutPromoCode()->subscriptionsOnly()->withSubscriptionTiers([
                    Subscription::VIP_UNLIMITED,
                ])->count(),
                0,
                //TODO: Corporate Credit Plans - Confirm the criteria for an order that has corporate credit plansphp
            ],
            'backgroundColor' => collect(PurchaseTypes::ALL)
                ->map(fn (string $orderable) => HexadecimalColorCode::get($orderable))
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
