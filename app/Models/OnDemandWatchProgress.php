<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OnDemandWatchProgress
 *
 * @property int $id
 * @property int $ondemand_id
 * @property int $user_id
 * @property int $time
 * @property int $completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $time_seconds
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereOndemandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnDemandWatchProgress whereUserId($value)
 * @mixin \Eloquent
 */
class OnDemandWatchProgress extends Model
{
    protected $table = 'on_demand_watch_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ondemand_id',
        'user_id',
        'time',
        'completed',
    ];

    protected $appends = [
        'time_seconds',
    ];

    public function getTimeSecondsAttribute()
    {
        return ($this->time / 100);
    }
}
