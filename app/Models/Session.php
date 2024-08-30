<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Session
 *
 * @property int $id
 * @property int $member_id
 * @property int $trainer_id
 * @property int|null $user_package_id
 * @property string|null $datetime
 * @property string $date
 * @property string $start
 * @property int $length
 * @property int|null $rating
 * @property string|null $feedback
 * @property float|null $price
 * @property string $status
 * @property string|null $confirmed_at
 * @property string|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $end
 * @property-read mixed $length_human
 * @property-read mixed $start_human
 * @property-read mixed $status_human
 * @property-read Carbon $time_human
 * @property-read mixed $type
 * @property-read \App\Models\SessionZoomLink|null $link
 * @property-read \App\Models\User|null $member
 * @property-read \App\Models\MemberPackage|null $package
 * @property-read \App\Models\User|null $trainer
 * @method static \Illuminate\Database\Eloquent\Builder|Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Session query()
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereTrainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session whereUserPackageId($value)
 * @mixin \Eloquent
 */
class Session extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'trainer_id',
        'user_package_id',
        'datetime',
        'date',
        'start',
        'length',
        'rating',
        'feedback',
        'status',
        'confirmed_at',
        'cancelled_at',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'time_human',
        'start_human',
        'length_human',
        'end',
        'status_human',
        'type',
    ];

    public function member()
    {
        return $this->belongsTo('App\Models\User', 'member_id', 'id');
    }

    public function trainer()
    {
        return $this->belongsTo('App\Models\User', 'trainer_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo('App\Models\MemberPackage', 'user_package_id', 'id');
    }

    public function link()
    {
        return $this->hasOne('App\Models\SessionZoomLink', 'session_id', 'id');
    }

    public function getTimeHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['datetime'])->format('g:ia l dS F Y');
    }

    /**
     * Return formated time string
     *
     */
    public function getStartHumanAttribute()
    {
        return Carbon::parse($this->attributes['date'] . ' ' . $this->attributes['start'])->format('g:ia');
    }

    public function getEndAttribute()
    {
        return Carbon::parse($this->attributes['datetime'])->addMinutes($this->length)->format('H:i:s');
    }

    public function getLengthHumanAttribute()
    {
        return $this->length;
    }

    public function getStatusHumanAttribute()
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }

        if ($this->status === 'noshow') {
            return 'No Show';
        }

        if ($this->datetime < date('Y-m-d H:i:s')) {
            return 'Completed';
        }

        if ($this->datetime >= date('Y-m-d H:i:s')) {
            return 'Upcoming';
        }

        return $this->status;
    }

    public function getTypeAttribute()
    {
        return 'session';
    }
}
