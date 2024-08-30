<?php

namespace App\Models;

use App\Collections\OrderCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use JetBrains\PhpStorm\Pure;

/**
 * Class Order
 *
 * @property int $id
 * @property Carbon $created_at
 * @property float $value
 * @property string $promo_code
 * @property mixed $orderable
 * @property mixed $member_id
 * @property int $orderable_id
 * @property string $orderable_type
 * @property mixed $relatedOrders
 * @package App\Models
 * @property string $method
 * @property \Illuminate\Support\Carbon|null $expires
 * @property string $stripe_order_id
 * @property string|null $invoice_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $created_human
 * @property-read String $method_human
 * @property-read String $orderable_type_human
 * @property-read String $value_human
 * @property-read \App\Models\User|null $member
 * @property-read OrderCollection|Order[] $olderRelatedOrders
 * @property-read int|null $older_related_orders_count
 * @property-read int|null $related_orders_count
 * @method static OrderCollection|static[] all($columns = ['*'])
 * @method static Builder|Order createdMonth($year, $month)
 * @method static Builder|Order createdThisWeek()
 * @method static Builder|Order createdToday()
 * @method static Builder|Order createdYear($year)
 * @method static \Database\Factories\OrderFactory factory(...$parameters)
 * @method static OrderCollection|static[] get($columns = ['*'])
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order onlyCertainSubscriptionTypes(array $subscriptionTypes)
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static Builder|Order query()
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDeletedAt($value)
 * @method static Builder|Order whereExpires($value)
 * @method static Builder|Order whereHasPaymentMethods($paymentMethods)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereInvoiceId($value)
 * @method static Builder|Order whereMemberId($value)
 * @method static Builder|Order whereMethod($value)
 * @method static Builder|Order whereOrderableId($value)
 * @method static Builder|Order whereOrderableType($value)
 * @method static Builder|Order wherePromoCode($value)
 * @method static Builder|Order whereStripeOrderId($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static Builder|Order withoutAppends()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
class Order extends Model
{
    use SoftDeletes, HasFactory;

    const METHOD_STRIPE = 'stripe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'value',
        'method',
        'expires',
        'orderable_id',
        'orderable_type',
        'stripe_order_id',
        'invoice_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'created_human',
        'orderable_type_human',
        'method_human',
        'value_human',
    ];

    protected $dates = [
        'expires',
    ];

    protected $casts = [
        'member_id' => 'integer',
    ];

    public static bool $withoutAppends = false;

    /**
     * @param array $models
     * @return OrderCollection
     */
    public function newCollection(array $models = []): OrderCollection
    {
        return new OrderCollection($models);
    }

    public function scopeWithoutAppends($query)
    {
        self::$withoutAppends = true;

        return $query;
    }

    /**
     * @return bool
     */
    #[Pure] public function isForThirtyPackCredits(): bool
    {
        return $this->isOfCreditPack(30);
    }

    /**
     * @return bool
     */
    #[Pure] public function isForTenPackCredits(): bool
    {
        return $this->isOfCreditPack(10);
    }

    /**
     * @return bool
     */
    #[Pure] public function isForOnePackCredits(): bool
    {
        return $this->isOfCreditPack(1);
    }

    /**
     * @return bool
     */
    #[Pure] public function isForIntroPackCredits(): bool
    {
        return $this->isOfCreditPack(3);
    }

    /**
     * @return string[]
     */
    protected function getArrayableAppends(): array
    {
        if (self::$withoutAppends) {
            return ['orderable_type_human'];
        }

        return parent::getArrayableAppends();
    }

    /**
     * Get the parent orderable model (podcast, class, workout).
     */
    public function orderable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship for the member that made this order.
     * @return HasOne
     */
    public function member(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'member_id');
    }

    /**
     * @return string
     */
    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    /**
     * Return the human readable polymorphic relation type.
     *
     * @return String
     *
     */
    public function getOrderableTypeHumanAttribute(): string
    {
        if ($this->orderable_type === 'App\Models\Subscription') {
            return 'Subscriptions';
        }

        if ($this->orderable_type === 'App\Models\Podcast') {
            return 'Podcast';
        }

        if ($this->orderable_type === 'App\Models\OnDemand') {
            return 'On Demand Class';
        }

        if ($this->orderable_type === 'App\Models\Workout') {
            return 'Workout Class';
        }

        if ($this->orderable_type === 'App\Models\Course') {
            return 'Course Class';
        }

        if ($this->orderable_type === 'App\Models\CreditPack' || $this->orderable_type === 'App\Models\CreditPackPurchase') {
            return 'Credit Pack';
        }

        return 'Unknown';
    }

    /**
     * Return human readable method.
     *
     * @return String
     *
     */
    public function getMethodHumanAttribute(): string
    {
        if ($this->method === 'stripe') {
            return 'Stripe';
        }

        if ($this->method === 'apple') {
            return 'Apple In-App Purchase';
        }

        if ($this->method === 'google') {
            return 'Google In-App Purchase';
        }

        return $this->method;
    }

    /**
     * Return human readable value.
     *
     * @return String
     *
     */
    public function getValueHumanAttribute(): string
    {
        if ($this->value == 0) {
            return 'FREE';
        }

        return '£' . number_format($this->value / 100, 2);
    }

    /**
     * Scope a query to only orders created today.
     * @param Builder $query
     *
     * @return Builder
     *
     */
    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only orders created this week.
     * @param Builder $query
     *
     * @return Builder
     *
     */
    public function scopeCreatedThisWeek(Builder $query): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->startOfWeek()->format('Y-m-d') . ' 00:00:00')
            ->where('created_at', '<=', Carbon::now()->format('Y-m-d H:i:s'));
    }

    /**
     * Scope a query to only orders created in a specific month and year.
     * @param Builder $query
     * @param $year
     * @param $month
     * @return Builder
     */
    public function scopeCreatedMonth(Builder $query, $year, $month): Builder
    {
        return $query->whereYear('orders.created_at', $year)->whereMonth('orders.created_at', $month);
    }

    /**
     * Scope a query to only orders created in a specific year.
     * @param Builder $query
     * @param $year
     * @return Builder
     */
    public function scopeCreatedYear(Builder $query, $year): Builder
    {
        return $query->whereYear('created_at', $year);
    }

    /**
     * Scope a query to only include the supplied payment methods.
     * @param Builder $query
     * @param $paymentMethods
     * @return Builder
     */
    public function scopeWhereHasPaymentMethods(Builder $query, $paymentMethods): Builder
    {
        return $query->whereIn('method', $paymentMethods);
    }

    public function scopeOnlyCertainSubscriptionTypes(Builder $query, array $subscriptionTypes): Builder
    {
        return $query->when(
            $subscriptionTypes,
            fn (Builder $query, array $types) => $query->whereHasMorph(
                'orderable',
                [Subscription::class],
                fn (Builder $query) => $query->whereIn('tier', $types)
            )
        );
    }

    /**
     * @param int $credits
     * @return bool
     */
    protected function isOfCreditPack(int $credits): bool
    {
        return $this->orderable_type === CreditPackPurchase::class
            && $this->orderable
            && intval(optional($this->orderable->pack)->studio_credits) === $credits;
    }

    public function relatedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'member_id', 'member_id')
            ->where([
                ['id', '<>', $this->id],
                ['member_id', $this->member_id],
            ])
            ->when(
                $this->orderable_type === CreditPackPurchase::class,
                function (Builder $query) {
                    $query->whereHasMorph(
                        'orderable',
                        [CreditPackPurchase::class],
                        fn (Builder $query) => $query->where('credit_pack_id',
                            optional($this->orderable)->credit_pack_id)
                    );
                },
                function (Builder $query) {
                    $query->where([
                        ['orderable_type', $this->orderable_type],
                        ['orderable_id', $this->orderable_id],
                    ]);
                }
            );
    }

    public function olderRelatedOrders(): HasMany
    {
        return $this->relatedOrders()
            ->when($this->expires, function (Builder $query) {
                $query->where('expires', '<', $this->expires);
            })
            ->where([
                ['expires', '!=', null],
            ])
            ->whereHasMorph(
                'orderable',
                [CreditPackPurchase::class],
                fn (Builder $query) => $query->whereHas('pack', fn (Builder $query) => $query->nonPromotional())
            );
    }

    public function isARenewal(): bool
    {
        return $this->olderRelatedOrders->isNotEmpty();
    }
}
