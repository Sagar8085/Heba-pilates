<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductResource
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Http\Resources
 */
class ProductResource extends JsonResource
{
    /**
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => number_format($this->price, 2),
            'active' => $this->active,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
