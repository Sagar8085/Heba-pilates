<?php

namespace App\Models;

use Database\Factories\SubscriptionTierFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\SubscriptionTier
 *
 * @property int|null $id
 * @property string|null $name
 * @property string|null $slug
 * @property int|null $price
 * @property string|null $product_group
 * @property string|null $product_id
 * @property string|null $stripe_product_id
 * @property string|null $stripe_price_id
 * @property int|null $online_credits
 * @property int|null $studio_credits
 * @property int|null $admin_only
 * @property int|null $months_till_expiry
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $benefits
 * @property-read mixed $billing_period
 * @property-read mixed $price_human
 * @property-read Collection|SubscriptionTierPrice[] $subscription_tier_prices
 * @property-read int|null $subscription_tier_prices_count
 * @method static Builder|SubscriptionTier byGym(Gym $gym)
 * @method static SubscriptionTierFactory factory(...$parameters)
 * @method static Builder|SubscriptionTier newModelQuery()
 * @method static Builder|SubscriptionTier newQuery()
 * @method static Builder|SubscriptionTier query()
 * @method static Builder|SubscriptionTier whereAdminOnly($value)
 * @method static Builder|SubscriptionTier whereCreatedAt($value)
 * @method static Builder|SubscriptionTier whereId($value)
 * @method static Builder|SubscriptionTier whereMonthsTillExpiry($value)
 * @method static Builder|SubscriptionTier whereName($value)
 * @method static Builder|SubscriptionTier whereOnlineCredits($value)
 * @method static Builder|SubscriptionTier wherePrice($value)
 * @method static Builder|SubscriptionTier whereProductGroup($value)
 * @method static Builder|SubscriptionTier whereProductId($value)
 * @method static Builder|SubscriptionTier whereSlug($value)
 * @method static Builder|SubscriptionTier whereStripePriceId($value)
 * @method static Builder|SubscriptionTier whereStripeProductId($value)
 * @method static Builder|SubscriptionTier whereStudioCredits($value)
 * @method static Builder|SubscriptionTier whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SubscriptionTier extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_tiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'product_id',
        'online_credits',
        'studio_credits',
        'stripe_price_id',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var Array
     */
    protected $appends = [
        'price_human',
        'benefits',
        'billing_period',
    ];

    public function getTierPrice(): ?SubscriptionTierPrice
    {
        $gymId = 1;

        /** @var Gym|null $gym */
        $gym = auth()?->user()?->memberProfile?->homeStudio;

        if ($gym?->id) {
            $gymId = $gym->id;
        }

        return Gym::find($gymId)
            ?->subscription_tier_prices()
            ->where('subscription_tier_id', '=', $this->id)
            ->first();
    }

    public function getPriceHumanAttribute()
    {
        return ($this->price / 100);
    }

    public function getBenefitsAttribute()
    {
        $benefits = [];

        // @FIXME no, in general.
        $normalSingleSessionCreditPackPrice = 24;
        $londonSingleSessionCreditPackPrice = 30;
        $gym = auth()?->user()?->memberProfile?->homeStudio ?? Gym::find(1);
        $singleSessionPrice = match ($gym->id) {
            1, 2, 3 => $normalSingleSessionCreditPackPrice,
            5 => $londonSingleSessionCreditPackPrice,
        };

        if ($this->slug === 'standard') {
            $benefits = collect([
                "5 In-Studio Visits Per Month (worth £$singleSessionPrice each)",
                'Saving of £60 compared to individual purchases',
                'Preferential advanced booking allowance: 1 month in advance',
                // 'All session credits are valid for 60 days from date of activation.',
                // 'Additional sessions on top of membership allowance = £12 each.'
            ]);
        }

        if ($this->slug === 'premium') {
            $benefits = collect([
                "10 In-Studio Visits Per Month (worth £$singleSessionPrice each)",
                'Saving of £130 compared to individual purchases',
                'Preferential advanced booking allowance: 1 month in advance',
                // 'All session credits are valid for 60 days from date of activation.',
                // 'Additional sessions on top of membership allowance = £12 each.'
            ]);
        }

        if ($this->slug === 'premium-membership') {
            $benefits = collect([
                "10 In-Studio Visits Per Month (worth £$singleSessionPrice each)",
                'Preferential advanced booking allowance: 1 month in advance',
            ]);
        }

        if ($this->slug === 'once-weekly') {
            $benefits = collect([
                "4 In-Studio Sessions Per Month (worth £$singleSessionPrice each)",
            ]);
        }

        if ($this->slug === 'twice-weekly') {
            $benefits = collect([
                "8 In-Studio Sessions Per Month (worth £$singleSessionPrice each)",
            ]);
        }

        if ($this->slug === 'unlimited-membership-2') {
            $benefits = collect([
                "Unlimited In-Studio Visits Per Month (worth £$singleSessionPrice each)",
                '1 Guest Pass per month',
            ]);
        }

        if ($this->slug === 'unlimited-membership') {
            $benefits = collect([
                "Unlimited In-Studio Visits Per Month (worth £$singleSessionPrice each)",
                'Preferential advanced booking allowance: 1 month in advance',
            ]);
        }

        if ($this->slug === 'vip-unlimited' || $this->slug === 'vip-unlimited-2') {
            $benefits = collect([
                'Unlimited session allowance for full 12 month term of membership',
                'Book anytime to suit your schedule',
            ]);
        }

        return $benefits;
    }

    public function getBillingPeriodAttribute()
    {
        if ($this->slug === 'vip-unlimited' || $this->slug === 'vip-unlimited-2') {
            return 'Yearly';
        }

        return 'Monthly';
    }

    public function memberSubscription(User $user, bool $renews = false)
    {
        $expires = $this->months_till_expiry
            ? Carbon::now()->addMonths($this->months_till_expiry)
            : Carbon::now()->addMonth();

        return new Subscription([
            'user_id' => $user->id,
            'tier' => $this->slug,
            'expires' => $expires,
            'renew' => $renews,
            'online_credits' => $this->online_credits,
            'studio_credits' => $this->studio_credits,
            'stripe_id' => null,
            'stripe_payment_intent' => '',
        ]);
    }

    public function subscription_tier_prices(): HasMany
    {
        return $this->hasMany(SubscriptionTierPrice::class);
    }

    /**
     * @param User $user
     *
     * @return SubscriptionTierPrice
     */
    public function getPriceForMember(User $user): SubscriptionTierPrice
    {
        $gymId = $user->memberProfile?->home_studio_id ?? 1;

        return $this->subscription_tier_prices
            ->filter(
                fn (SubscriptionTierPrice $price): bool => $price->gyms->contains('id', $gymId),
            )
            ->first();
    }

    /**
     * Return only the SubscriptionTiers that have a SubscriptionTierPrice associated with
     * the given gym.
     *
     * @param Builder $creditPacks
     * @param Gym $gym
     *
     * @return Builder
     */
    public function scopeByGym(Builder $creditPacks, Gym $gym): Builder
    {
        $whereHasGym = fn (HasMany|Builder $subscriptionTierPrices
        ): HasMany|Builder => $subscriptionTierPrices->whereHas(
            'gyms',
            fn (Builder $gyms): Builder => $gyms->where('gyms.id', '=', $gym->id),
        );

        return $creditPacks
            ->with([
                'subscription_tier_prices' => $whereHasGym,
            ])
            ->whereHas('subscription_tier_prices', $whereHasGym);
    }
}
