<?php

namespace App\Charts\Leads\Pie;

use App\Charts\LeadPieChart;
use App\Collections\LeadCollection;
use App\Constants\PromotionConversionTypes;
use App\Helpers\HexadecimalColorCode;

/**
 * Class PromotionConversions
 *
 * @package App\Charts;
 */
class PromotionConversions extends LeadPieChart
{
    public function getLabels(): array
    {
        return PromotionConversionTypes::ALL;
    }

    public function buildPayload(LeadCollection $leads): array
    {
        return [
            'data' => [
                $leads->withoutConversions()->count(),
                $leads->convertedToSubscription()->count(),
                $leads->convertedToCreditPack()->count(),
            ],
            'backgroundColor' => collect(PromotionConversionTypes::ALL)
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