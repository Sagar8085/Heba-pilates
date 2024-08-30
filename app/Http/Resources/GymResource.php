<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GymResource
 *
 * @package App\Http\Resources
 */
class GymResource extends JsonResource
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
