<?php

namespace App\Charts\Orders\Pie;

use App\Charts\OrderPieChart;
use App\Collections\OrderCollection;
use App\Constants\CreditPacks;
use App\Constants\Subscriptions;
use App\Helpers\HexadecimalColorCode;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class VolumeOfCreditPackSales
 *
 * @package App\Charts;
 */
class VolumeOfCreditPackSales extends OrderPieChart
{
    public function getLabels(): array
    {
        return CreditPacks::ALL;
    }

    #[ArrayShape(['data' => "array", 'backgroundColor' => "array"])]
    public function buildPayload(OrderCollection $orders): array
    {
        return [
            'data' => [
                $orders->withThirtyPackCredits()->count(),
                $orders->withTenPackCredits()->count(),
                $orders->withOnePackCredits()->count(),
                $orders->withIntroPackCredits()->count(),
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