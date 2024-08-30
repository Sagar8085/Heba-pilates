<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use App\Jobs\UpdateUserSubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use stdClass;
use Stripe\StripeClient;

/**
 * App\Models\Subscription
 *
 * @method static find(int $orderable_id)
 * @property int $id
 * @property int $user_id
 * @property string $tier
 * @property \Illuminate\Support\Carbon|null $expires
 * @property int $renew
 * @property int $online_credits
 * @property int $studio_credits
 * @property string $billing_type
 * @property string|null $stripe_id
 * @property string $stripe_payment_intent
 * @property int|null $pause_days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $created_human
 * @property-read string $expires_human
 * @property-read string $method_human
 * @property-read String $name
 * @property-read String $online_credits_human
 * @property-read mixed $status_human
 * @property-read String $studio_credits_human
 * @property-read string $value_human
 * @property-read \App\Models\User|null $member
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\SubscriptionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Query\Builder|Subscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereBillingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereOnlineCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription wherePauseDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripePaymentIntent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStudioCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Subscription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Subscription withoutTrashed()
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use SoftDeletes, HasFactory;

    public const ALL = [
        self::PREMIUM_MEMBERSHIP_SUBSCRIPTION,
        self::ONE_MONTH_UNLIMITED,
        self::PREMIUM_MEMBERSHIP,
        self::VIP_UNLIMITED,
        self::UNLIMITED_MEMBERSHIP,
        self::UNLIMITED_MEMBERSHIP_SUBSCRIPTION,
        self::PREMIUM,
        self::STANDARD,
    ];

    public const PREMIUM_MEMBERSHIP_SUBSCRIPTION = 'premium-membership-subscription';
    public const ONE_MONTH_UNLIMITED = 'one-month-unlimited';
    public const PREMIUM_MEMBERSHIP = 'premium-membership';
    public const VIP_UNLIMITED = 'vip-unlimited';
    public const UNLIMITED_MEMBERSHIP = 'unlimited-membership';
    public const UNLIMITED_MEMBERSHIP_SUBSCRIPTION = 'unlimited-membership-subscription';
    public const PREMIUM = 'premium';
    public const STANDARD = 'standard';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'tier',
        'expires',
        'renew',
        'online_credits',
        'studio_credits',
        'stripe_id',
        'stripe_payment_intent',
        'pause_days',
        'billing_type',
    ];

    /**
     * The attributes that are added to every response.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'online_credits_human',
        'studio_credits_human',
        'created_human',
        'expires_human',
        'status_human',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($subscription) {
            $user = User::where('id', $subscription->user_id)->withTrashed()->first();

            if ($user !== null) {
                UpdateUserSubscriptionStatus::dispatch($user)->onQueue('subscription-status');

                $user->guest_status = $user->getGuestStatus();
                $user->save();
            }
        });
    }

    /**
     * Nice human readable name of the subscription.
     * @return String $name
     *
     *
     */
    public function getNameAttribute(): string
    {
        if ($this->tier === 'online-only') {
            return 'Online Only';
        }

        if ($this->tier === 'one-month-unlimited') {
            return '1 Month Unlimited';
        }

        if ($this->tier === 'standard') {
            return 'Standard';
        }

        if ($this->tier === 'vip-unlimited' || $this->tier === 'vip-unlimited-2') {
            return 'Unlimited Annual (VIP)';
        }

        if ($this->tier === 'twice-weekly' || $this->tier === 'twice-weekly-subscription') {
            return 'Twice Weekly';
        }

        if ($this->tier === 'once-weekly' || $this->tier === 'once-weekly-subscription') {
            return 'Once Weekly';
        }

        if ($this->tier === 'unlimited-membership' || $this->tier === 'unlimited-membership-subscription' || $this->tier === 'unlimited-membership-2' || $this->tier === 'unlimited-membership-subscription-2') {
            return 'Unlimited';
        }

        if ($this->tier === 'premium' || $this->tier === 'premium-membership' || $this->tier === 'premium-membership-subscription') {
            return 'Premium';
        }

        return $this->tier;
    }

    /**
     * Nice human readable number of online credits remaining.
     * @return String $onlineCreditsHuman
     *
     *
     */
    public function getOnlineCreditsHumanAttribute(): string
    {
        return 'Unlimited';

        if ($this->tier === 'online-only') {
            return 'Unlimited';
        }

        return $this->online_credits;
    }

    /**
     * Nice human readable number of studio credits remaining.
     * @return String $studioCreditsHuman
     *
     *
     */
    public function getStudioCreditsHumanAttribute(): string
    {
        if ($this->tier === 'online-only') {
            return 'None';
        }

        if ($this->studio_credits > 5000) {
            return 'Unlimited';
        }

        return $this->studio_credits;
    }

    /**
     * @return HasOne
     *
     * TODO: Phase out this named function of 'member' as this is actually a user model. Use user method instead
     */
    public function member(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    /**
     * Relationship for the order.
     * @return HasOne
     *
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'orderable_id', 'id')
            ->where('orders.orderable_type', Subscription::class);
    }

    /**
     * @return string
     */
    public function getMethodHumanAttribute(): string
    {
        return 'Apple';
    }

    /**
     * @return mixed
     */
    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F Y');
    }

    /**
     * @return string
     */
    public function getValueHumanAttribute(): string
    {
        return '£49.99';
    }

    /**
     * @return string
     */
    public function getExpiresHumanAttribute(): string
    {
        if ($this->expires) {
            return $this->expires->format('l dS F Y');
        }

        return '';
    }

    public function getStatusHumanAttribute()
    {
        if ($this->deleted_at) {
            return 'Deleted';
        } else {
            if ($this->renew) {
                return 'Active - Renews';
            } else {
                if ($this->expires < date('Y-m-d H:i:s')) {
                    return 'Expired';
                } else {
                    if (!$this->renew && $this->expires > date('Y-m-d H:i:s')) {
                        return 'Active - Does Not Renew';
                    }
                }
            }
        }
    }

    public function sendPurchaseConfirmationEmail()
    {
        $user = User::where('id', $this->user_id)->withTrashed()->first();
        SendEmailJob::dispatch($user, 'emails.user.membership-purchase', 'Your Heba Pilates Membership',
            ['user' => $user])->onQueue('account-notifications');
    }

    private function getStripeSecret()
    {
        return config('services.stripe.secret');
    }

    public function cancel($type = 'Web')
    {
        $this->update([
            'renew' => 0,
        ]);

        Event::create([
            'message' => 'Cancelled automatic renewal via ' . $type,
            'user_id' => auth()->user()->id,
            'object_id' => $this->id,
            'object_type' => 'App\Models\Subscription',
            'created_by' => auth()->user()->id,
        ]);

        if ($this->billing_type === 'automatic') {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $stripe->subscriptions->update($this->stripe_id,
                ['metadata' => ['cancelled_by' => '#' . auth()->user()->id . ' ' . auth()->user()->name]]);
            $stripe->subscriptions->cancel($this->stripe_id, []);
        }

        $this->sendCancellationEmails();
    }

    public function sendCancellationEmails()
    {
        $user = User::where('id', $this->user_id)->withTrashed()->first();
        SendEmailJob::dispatch($user, 'emails.user.membership-cancelled',
            'Confirmation of Heba Pilates subscription cancellation',
            ['user' => $user, 'subscription' => $this])->onQueue('account-notifications');

        $admin = new stdClass();
        $admin->email = ['hello@hebapilates.com', 'customerservice@hebapilates.com', 'steve@hebapilates.com'];
        SendEmailJob::dispatch($admin, 'emails.admin.membership-cancelled',
            'Cancellation request by ' . $user->full_name . ', #' . $user->id,
            ['user' => $user])->onQueue('account-notifications');
    }
}
