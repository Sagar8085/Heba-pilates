<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ReformerBookingResource
 *
 * @package App\Http\Resources
 */
class ReformerBookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
