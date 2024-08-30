<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Availability
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $hours
 * @property string $date
 * @property string $start
 * @property string $end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $duration
 * @property-read mixed $end_hour
 * @property-read mixed $end_human
 * @property-read mixed $finishing_time
 * @property-read mixed $start_hour
 * @property-read mixed $start_human
 * @property-read mixed $start_minutes
 * @property-read mixed $starting_time
 * @property-read \App\Models\Gym|null $gym
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Availability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Availability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Availability query()
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Availability whereUserId($value)
 * @mixin \Eloquent
 */
class Availability extends Model
{
    protected $table = 'availability';

    protected $appends = [
        'start_hour',
        'end_hour',
        'start_minutes',
        'duration',
        'starting_time',
        'finishing_time',
        'start_human',
        'end_human',
    ];

    protected $fillable = [
        'user_id',
        'gym_id',
        'hours',
        'day',
        'start',
        'end',
        'date',
        'available',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function gym()
    {
        return $this->belongsTo('App\Models\Gym');
    }

    /**
     * Get nice human readable time
     *
     */
    public function getStartHumanAttribute()
    {
        return Carbon::parse($this->start)->format('H:ia');
    }

    /**
     * Get nice human readable time
     *
     */
    public function getEndHumanAttribute()
    {
        return Carbon::parse($this->end)->format('H:ia');
    }

    /**
     * Starting hour attribute
     *
     */
    public function getStartHourAttribute()
    {
        return date('H', strtotime($this->attributes['start']));
    }

    /**
     * Starting time attribute
     *
     */
    public function getStartingTimeAttribute()
    {
        return date('H:i', strtotime($this->attributes['start']));
    }

    /**
     * Finishing time attribute
     *
     */
    public function getFinishingTimeAttribute()
    {
        return date('H:i', strtotime($this->attributes['end']));
    }

    /**
     * End hour attribute
     *
     */
    public function getEndHourAttribute()
    {
        return date('H', strtotime($this->attributes['end']));
    }

    /**
     * Starting minutes attribute
     *
     */
    public function getStartMinutesAttribute()
    {
        return date('i', strtotime($this->attributes['start']));
    }

    /**
     * Availability duration attribute
     *
     */
    public function getDurationAttribute()
    {
        return ($this->end_hour - $this->start_hour);
    }
}
