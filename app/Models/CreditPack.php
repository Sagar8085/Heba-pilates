<?php

namespace App\Models;

use Database\Factories\CreditPackFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\CreditPack
 *
 * @property int|null $id
 * @property string|null $name
 * @property string|null $description
 * @property int $price
 * @property int|null $promotional
 * @property int|null $online_credits
 * @property int|null $studio_credits
 * @property int|null $days_till_expiry
 * @property int|null $months_till_expiry
 * @property string $stripe_price_id
 * @property string|null $stripe_product_id
 * @property string|null $apple_product_id
 * @property string|null $google_product_id
 * @property int|null $admin_only
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|CreditPackPrice[] $credit_pack_prices
 * @property-read int|null $credit_pack_prices_count
 * @property-read string $price_human
 * @property-read mixed $studio_credits_human
 * @property-read Order|null $order
 * @method static Builder|CreditPack byGym(Gym $gym)
 * @method static CreditPackFactory factory(...$parameters)
 * @method static Builder|CreditPack newModelQuery()
 * @method static Builder|CreditPack newQuery()
 * @method static Builder|CreditPack nonPromotional()
 * @method static Builder|CreditPack promotional()
 * @method static Builder|CreditPack query()
 * @method static Builder|CreditPack whereAdminOnly($value)
 * @method static Builder|CreditPack whereAppleProductId($value)
 * @method static Builder|CreditPack whereCreatedAt($value)
 * @method static Builder|CreditPack whereDaysTillExpiry($value)
 * @method static Builder|CreditPack whereDescription($value)
 * @method static Builder|CreditPack whereGoogleProductId($value)
 * @method static Builder|CreditPack whereId($value)
 * @method static Builder|CreditPack whereMonthsTillExpiry($value)
 * @method static Builder|CreditPack whereName($value)
 * @method static Builder|CreditPack whereOnlineCredits($value)
 * @method static Builder|CreditPack wherePrice($value)
 * @method static Builder|CreditPack wherePromotional($value)
 * @method static Builder|CreditPack whereStripePriceId($value)
 * @method static Builder|CreditPack whereStripeProductId($value)
 * @method static Builder|CreditPack whereStudioCredits($value)
 * @method static Builder|CreditPack whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CreditPack extends Model
{
    use HasFactory;

    /**
     * The attributes that are guarded
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'price_human',
        'studio_credits_human',
    ];

    private function getPackPrice(): ?CreditPackPrice
    {
        $gymId = 1;

        /** @var Gym|null $gym */
        $gym = auth()?->user()?->memberProfile?->homeStudio;

        if ($gym?->id) {
            $gymId = $gym->id;
        }

        return Gym::find($gymId)
            ?->credit_pack_prices()
            ->where('credit_pack_id', '=', $this->id)
            ->first();
    }

    public function getPriceAttribute(): int
    {
        $price = 99999;

        $creditPackPrice = $this->getPackPrice();

        if ($creditPackPrice instanceof CreditPackPrice) {
            $price = $creditPackPrice->price_in_pence;
        }

        return $price;
    }

    /**
     * @return string
     */
    public function getPriceHumanAttribute(): string
    {
        return number_format($this->price / 100);
    }

    /**
     * Get the on demands's order by polymorphic relation.
     */
    public function order(): MorphOne
    {
        return $this->morphOne(Order::class, 'orderable');
    }

    public function credit_pack_prices(): HasMany
    {
        return $this->hasMany(CreditPackPrice::class);
    }

    public function getStudioCreditsHumanAttribute()
    {
        if ($this->id === 11) {
            return 'Unlimited';
        }

        return $this->studio_credits;
    }

    public function scopePromotional(Builder $query): Builder
    {
        return $query->wherePromotional(true);
    }

    public function scopeNonPromotional(Builder $query): Builder
    {
        return $query->wherePromotional(false);
    }

    /**
     * Return only the CreditPacks that have a CreditPackPrice associated with
     * the given gym.
     *
     * @param Builder $creditPacks
     * @param Gym $gym
     *
     * @return Builder
     */
    public function scopeByGym(Builder $creditPacks, Gym $gym): Builder
    {
        // I'm open to suggestions on a better way to do this.

        $whereHasGym = fn (HasMany|Builder $creditPackPrices): HasMany|Builder => $creditPackPrices->whereHas(
            'gyms',
            fn (Builder $gyms): Builder => $gyms->where('gyms.id', '=', $gym->id),
        );

        return $creditPacks
            ->with([
                'credit_pack_prices' => $whereHasGym,
            ])
            ->whereHas('credit_pack_prices', $whereHasGym);
    }

    public function purchaseExpiration(): ?Carbon
    {
        if ($this->days_till_expiry) {
            return Carbon::now()->addDays($this->days_till_expiry);
        }

        if ($this->months_till_expiry) {
            return Carbon::now()->addMonths($this->months_till_expiry);
        }

        return null;
    }

    public function generatePurchase(User $user): CreditPackPurchase
    {
        return new CreditPackPurchase([
            'user_id' => $user->id,
            'credit_pack_id' => $this->id,
            'online_credits' => $this->online_credits,
            'studio_credits' => $this->studio_credits,
            'expires' => $this->purchaseExpiration(),
            'order_id' => 0,
        ]);
    }

    /**
     * @param User $user
     *
     * @return CreditPackPrice
     */
    public function getPriceForMember(User $user): CreditPackPrice
    {
        $gymId = $user->memberProfile?->home_studio_id ?? 1;

        return $this->credit_pack_prices
            ->filter(
                fn (CreditPackPrice $price): bool => $price->gyms->contains('id', $gymId),
            )
            ->first();
    }
}
