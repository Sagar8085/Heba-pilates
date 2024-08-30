<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\LiveClassBooking
 *
 * @property int $id
 * @property int $liveclass_id
 * @property int $member_id
 * @property string|null $booked_using_type
 * @property int|null $booked_using_id
 * @property string|null $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $bookable
 * @property-read \App\Models\LiveClass|null $class
 * @property-read \App\Models\User|null $member
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking newQuery()
 * @method static \Illuminate\Database\Query\Builder|LiveClassBooking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereBookedUsingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereBookedUsingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereLiveclassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassBooking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LiveClassBooking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LiveClassBooking withoutTrashed()
 * @mixin \Eloquent
 */
class LiveClassBooking extends Model
{
    use SoftDeletes;

    protected $table = 'live_classes_bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'liveclass_id',
        'member_id',
        'booked_using_type',
        'booked_using_id',
        'location',
    ];

    /**
     * Get the parent orderable model (podcast, class, workout).
     */
    public function bookable()
    {
        return $this->morphTo();
    }

    public function class()
    {
        return $this->hasOne('App\Models\LiveClass', 'id', 'liveclass_id');
    }

    public function member()
    {
        return $this->hasOne('App\Models\User', 'id', 'member_id');
    }
}
