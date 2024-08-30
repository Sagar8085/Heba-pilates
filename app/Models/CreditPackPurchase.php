<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use App\Jobs\UpdateUserSubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CreditPackPurchase
 *
 * @property int $id
 * @property int $user_id
 * @property int $credit_pack_id
 * @property int $order_id
 * @property int $online_credits
 * @property int $studio_credits
 * @property \Illuminate\Support\Carbon|null $expires
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read Carbon $created_human
 * @property-read mixed $deleted_at_human
 * @property-read mixed $expired
 * @property-read mixed $expires_human
 * @property-read \App\Models\User|null $member
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\CreditPack|null $pack
 * @method static \Database\Factories\CreditPackPurchaseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase newQuery()
 * @method static \Illuminate\Database\Query\Builder|CreditPackPurchase onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase query()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereCreditPackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereOnlineCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereStudioCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditPackPurchase whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CreditPackPurchase withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CreditPackPurchase withoutTrashed()
 * @mixin \Eloquent
 */
class CreditPackPurchase extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'credit_packs_purchases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'credit_pack_id',
        'order_id',
        'online_credits',
        'studio_credits',
        'expires',
        'deleted_by',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'created_human',
        'expires_human',
        'expired',
        'deleted_at_human',
    ];

    protected $casts = [
        'expires' => 'datetime',
    ];

    public function pack(): HasOne
    {
        return $this->hasOne(CreditPack::class, 'id', 'credit_pack_id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function deleter(): HasOne
    {

        return $this->hasOne(User::class, 'id', 'deleted_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'object_id', 'id')
            ->where('object_type', CreditPackPurchase::class)
            ->orderBy('created_at', 'DESC')
            ->with('author');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($creditPackPurchase) {
            $user = User::where('id', $creditPackPurchase->user_id)->withTrashed()->first();

            if ($user !== null) {
                UpdateUserSubscriptionStatus::dispatch($user)->onQueue('subscription-status');

                $user->guest_status = $user->getGuestStatus();
                $user->save();
            }
        });
    }

    /**
     * Relationship for the order.
     *
     * @return HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'orderable_id', 'id')
            ->where('orders.orderable_type', CreditPackPurchase::class);
    }

    public function getDeletedAtHumanAttribute()
    {
        if ($this->deleted_at) {
            return Carbon::parse($this->deleted_at)->format('g:ia l dS F Y');
        }

        return null;
    }

    public function getExpiresHumanAttribute()
    {
        if ($this->expires !== null) {
            return $this->expires->format('dS F Y \a\t H:i');
        }

        return 'No Expiry';
    }

    public function getExpiredAttribute()
    {
        if ($this->expires !== null && $this->expires < date('Y-m-d H:i:s')) {
            return true;
        }

        return false;
    }

    /**
     * Get the human formatted created at attribute.
     *
     * @param None
     *
     * @return Carbon
     */
    public function getCreatedHumanAttribute()
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function sendPurchaseConfirmationEmail()
    {
        $user = User::where('id', $this->user_id)->withTrashed()->first();
        SendEmailJob::dispatch($user, 'emails.user.credit-purchase', 'Your Heba Pilates Credit Pack',
            ['user' => $user, 'credit' => $this->pack->name])->onQueue('account-notifications');
    }
}
