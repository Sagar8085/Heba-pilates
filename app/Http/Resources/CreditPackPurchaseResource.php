<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CreditPackPurchaseResource
 *
 * @package App\Http\Resources
 */
class CreditPackPurchaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return array_merge(
            parent::toArray($request),
            [
                'credit_pack' => new CreditPackResource($this->whenLoaded('pack')),
            ]
        );
    }
}
