<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StripePaymentMethod
 *
 * @property int $id
 * @property int $user_id
 * @property string $payment_method
 * @property string $type
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePaymentMethod whereUserId($value)
 * @mixin \Eloquent
 */
class StripePaymentMethod extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stripe_payment_methods';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'user_id',
        'payment_method',
        'type',
        'default',
    ];
}
