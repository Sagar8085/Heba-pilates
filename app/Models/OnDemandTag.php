<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OnDemandTag
 *
 * @property int $id
 * @property int $ondemand_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag whereOndemandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnDemandTag extends Model
{
    protected $table = '_pivot_on_demand_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'ondemand_id',
    ];
}
