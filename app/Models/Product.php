<?php

namespace App\Models;

use App\Filters\ProductFilter;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Product
 *
 * @property integer $id
 * @property string $name
 * @property integer $price
 * @property boolean $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static create(array $validated)
 * @method static filter(ProductFilter $filters)
 * @package App\Models
 * @method static \Database\Factories\ProductFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Base
{
    use HasFactory, Filterable;

    protected $casts = [
        'active' => 'boolean',
        'price' => 'integer',
    ];

    public function getPriceAttribute($value): float|int
    {
        return $value / 100;
    }
}
