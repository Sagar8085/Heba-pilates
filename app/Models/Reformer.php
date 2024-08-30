<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Reformer
 *
 * @property int $id
 * @property int $gym_id
 * @property string $name
 * @property string $status
 * @property string $serial_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReformerBooking[] $bookings
 * @property-read int|null $bookings_count
 * @property-read mixed $status_human
 * @property-read \App\Models\Gym|null $gym
 * @method static \Database\Factories\ReformerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer isWorking()
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reformer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reformer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gym_id',
        'name',
        'status',
        'serial_number',
    ];

    /**
     * The attributes that are added to every response.
     *
     * @var array
     */
    protected $appends = [
        'status_human',
    ];

    public function getStatusHumanAttribute()
    {
        return ucwords($this->status);
    }

    public function gym()
    {
        return $this->hasOne(Gym::class, 'id', 'gym_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ReformerBooking::class);
    }

    public function scopeIsWorking($query)
    {
        return $query->where('status', 'working');
    }
}
