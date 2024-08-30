<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OnDemandFavourite
 *
 * @property int $id
 * @property int $user_id
 * @property int $ondemand_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite whereOndemandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandFavourite whereUserId($value)
 * @mixin \Eloquent
 */
class OnDemandFavourite extends Model
{
    protected $table = '_pivot_on_demand_favourites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ondemand_id',
    ];
}
