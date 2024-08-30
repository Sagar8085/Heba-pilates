<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CartBooking
 *
 * @property int $id
 * @property int $member_id
 * @property int $trainer_id
 * @property int $package_id
 * @property Array $bookings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package|null $package
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking whereBookings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking whereTrainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartBooking whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CartBooking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'trainer_id',
        'package_id',
        'bookings',
    ];

    /**
     * connects the users custom workout pivot table
     */
    public function package()
    {
        return $this->hasOne('App\Models\Package', 'id', 'package_id');
    }

    public function getBookingsAttribute($value): mixed
    {
        return json_decode($value);
    }
}
