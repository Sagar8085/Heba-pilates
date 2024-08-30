<?php

namespace App\Models;

use App\Scopes\ExcludedGyms;
use Database\Factories\GymFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Gym
 *
 * @property int|null $id
 * @property string|null $name
 * @property string|null $corporate_name
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $street_address
 * @property string|null $city
 * @property string|null $postcode
 * @property string|null $lat
 * @property string|null $lng
 * @property array|null $operating_hours
 * @property array|null $trainer_break_times
 * @property string|null $image_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|CreditPackPrice[] $credit_pack_prices
 * @property-read int|null $credit_pack_prices_count
 * @property-read String $address
 * @property-read mixed $image
 * @property-read array $position
 * @property-read Collection|Reformer[] $reformers
 * @property-read int|null $reformers_count
 * @property-read Collection|SubscriptionTierPrice[] $subscription_tier_prices
 * @property-read int|null $subscription_tier_prices_count
 * @method static GymFactory factory(...$parameters)
 * @method static Builder|Gym filterAccess()
 * @method static Builder|Gym newModelQuery()
 * @method static Builder|Gym newQuery()
 * @method static Builder|Gym query()
 * @method static Builder|Gym visible()
 * @method static Builder|Gym whereCity($value)
 * @method static Builder|Gym whereCorporateName($value)
 * @method static Builder|Gym whereCreatedAt($value)
 * @method static Builder|Gym whereEmail($value)
 * @method static Builder|Gym whereId($value)
 * @method static Builder|Gym whereImagePath($value)
 * @method static Builder|Gym whereLat($value)
 * @method static Builder|Gym whereLng($value)
 * @method static Builder|Gym whereName($value)
 * @method static Builder|Gym whereOperatingHours($value)
 * @method static Builder|Gym wherePhoneNumber($value)
 * @method static Builder|Gym wherePostcode($value)
 * @method static Builder|Gym whereStreetAddress($value)
 * @method static Builder|Gym whereTrainerBreakTimes($value)
 * @method static Builder|Gym whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Gym extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'corporate_name',
        'operating_hours',
        'phone_number',
        'email',
        'street_address',
        'city',
        'postcode',
        'lat',
        'lng',
    ];

    /**
     * The attributes that are appended to every response.
     *
     * @var array
     */
    protected $appends = [
        'address',
        'position',
        'image',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'trainer_break_times' => 'array',
        'lat' => 'string',
        'lng' => 'string',
    ];

    public function getAddressAttribute(): string
    {
        return $this->street_address . ', ' . $this->city . ', ' . $this->postcode;
    }

    /**
     * A gym can have multiple reformer machines.
     * @return HasMany
     */
    public function reformers(): HasMany
    {
        return $this->hasMany(Reformer::class, 'gym_id', 'id');
    }

    public function subscription_tier_prices(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionTierPrice::class, '_pivot_gym_subscription_tier_price');
    }

    public function credit_pack_prices(): BelongsToMany
    {
        return $this->belongsToMany(CreditPackPrice::class, '_pivot_credit_pack_price_gym');
    }

    public function getPositionAttribute(): array
    {
        return [
            'lat' => (float)$this->lat,
            'lng' => (float)$this->lng,
        ];
    }

    public function getOperatingHoursAttribute($value)
    {
        return json_decode($value);
    }

    public function getImageAttribute()
    {
        return $this->image_path;
    }

    /**
     * Scope to filter query to only return Gyms that this admin is able to access.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFilterAccess(Builder $query): Builder
    {
        return $query->whereIn('id', auth()->user()->accessibleGymsArray());
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereNotIn('corporate_name', [
            'buchinger-wilhelmi',
            'marlow',
        ]);
    }
}
