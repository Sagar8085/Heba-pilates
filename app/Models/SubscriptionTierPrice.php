<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\SubscriptionTierPrice
 *
 * @property int $id
 * @property int $subscription_tier_id
 * @property string $stripe_price_id
 * @property string|null $name
 * @property int|null $price_in_pence
 * @property mixed|null $recurring
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Gym[] $gyms
 * @property-read int|null $gyms_count
 * @property-read SubscriptionTier|null $subscription_tier
 * @method static Builder|SubscriptionTierPrice newModelQuery()
 * @method static Builder|SubscriptionTierPrice newQuery()
 * @method static \Illuminate\Database\Query\Builder|SubscriptionTierPrice onlyTrashed()
 * @method static Builder|SubscriptionTierPrice query()
 * @method static Builder|SubscriptionTierPrice whereCreatedAt($value)
 * @method static Builder|SubscriptionTierPrice whereDeletedAt($value)
 * @method static Builder|SubscriptionTierPrice whereId($value)
 * @method static Builder|SubscriptionTierPrice whereName($value)
 * @method static Builder|SubscriptionTierPrice wherePriceInPence($value)
 * @method static Builder|SubscriptionTierPrice whereRecurring($value)
 * @method static Builder|SubscriptionTierPrice whereStripePriceId($value)
 * @method static Builder|SubscriptionTierPrice whereSubscriptionTierId($value)
 * @method static Builder|SubscriptionTierPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SubscriptionTierPrice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SubscriptionTierPrice withoutTrashed()
 * @mixin Eloquent
 */
class SubscriptionTierPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'stripe_price_id',
        'recurring',
        'price_in_pence',
    ];

    public function subscription_tier(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTier::class);
    }

    public function gyms(): BelongsToMany
    {
        return $this->belongsToMany(Gym::class, '_pivot_gym_subscription_tier_price');
    }
}
