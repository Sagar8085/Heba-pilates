<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveClass
 *
 * @property int $id
 * @property int $category_id
 * @property int $instructor_id
 * @property int $duration
 * @property int $reformer
 * @property string|null $datetime
 * @property string|null $description
 * @property string|null $room_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\LiveClassCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Equipment[] $equipment
 * @property-read int|null $equipment_count
 * @property-read mixed $can_join_stream
 * @property-read Carbon $date_human
 * @property-read Carbon $datetime_human
 * @property-read Carbon $time_human
 * @property-read \App\Models\User|null $instructor
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereReformer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereRoomToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClass whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LiveClass extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'instructor_id',
        'duration',
        'reformer',
        'datetime',
        'description',
        'room_token',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'datetime_human',
        'date_human',
        'time_human',
        'can_join_stream',
    ];

    public function category()
    {
        return $this->hasOne(LiveClassCategory::class, 'id', 'category_id');
    }

    public function instructor()
    {
        return $this->hasOne(User::class, 'id', 'instructor_id');
    }

    public function bookings()
    {
        return $this->belongsToMany('App\Models\User', 'live_classes_bookings', 'liveclass_id', 'member_id');
    }

    public function equipment()
    {
        return $this->belongsToMany('App\Models\Equipment', '_pivot_live_classes_equipment', 'liveclass_id',
            'equipment_id');
    }

    public function getDatetimeHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['datetime'])->format('g:ia l dS F Y');
    }

    public function getDateHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['datetime'])->format('dS F Y');
    }

    public function getTimeHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['datetime'])->format('g:ia');
    }

    public function getCanJoinStreamAttribute()
    {
        $startTime = $this->datetime;

        $minutesTillStream = Carbon::now()->diffInMinutes($startTime, false);

        if ($minutesTillStream < 5) {
            return true;
        }

        return false;
    }
}
