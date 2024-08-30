<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OnDemandView
 *
 * @property int $id
 * @property int $on_demand_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OnDemand|null $class
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView whereOnDemandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandView whereUserId($value)
 * @mixin \Eloquent
 */
class OnDemandView extends Model
{
    protected $table = 'on_demand_views';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'on_demand_id',
        'user_id',
        'created_at',
    ];

    public function class()
    {
        return $this->hasOne('App\Models\OnDemand', 'id', 'on_demand_id');
    }
}
