<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PivotOnDemandCategoryGym
 *
 * @property int $id
 * @property int $category_id
 * @property int $gym_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym query()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotOnDemandCategoryGym whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PivotOnDemandCategoryGym extends Model
{
    protected $table = '_pivot_on_demand_category_gyms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'gym_id',
    ];
}
