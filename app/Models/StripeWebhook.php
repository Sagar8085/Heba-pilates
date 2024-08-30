<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StripeWebhook
 *
 * @property int $id
 * @property mixed $response
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeWebhook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StripeWebhook extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stripe_webhooks';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'response',
        'type',
    ];
}
