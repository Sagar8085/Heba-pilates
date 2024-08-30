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
 * App\Models\CreditPackPrice
 *
 * @property int $id
 * @property int $credit_pack_id
 * @property string $stripe_price_id
 * @property string|null $name
 * @property int|null $price_in_pence
 * @property mixed|null $recurring
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read CreditPack|null $credit_pack
 * @property-read Collection|Gym[] $gyms
 * @property-read int|null $gyms_count
 * @method static Builder|CreditPackPrice newModelQuery()
 * @method static Builder|CreditPackPrice newQuery()
 * @method static \Illuminate\Database\Query\Builder|CreditPackPrice onlyTrashed()
 * @method static Builder|CreditPackPrice query()
 * @method static Builder|CreditPackPrice whereCreatedAt($value)
 * @method static Builder|CreditPackPrice whereCreditPackId($value)
 * @method static Builder|CreditPackPrice whereDeletedAt($value)
 * @method static Builder|CreditPackPrice whereId($value)
 * @method static Builder|CreditPackPrice whereName($value)
 * @method static Builder|CreditPackPrice wherePriceInPence($value)
 * @method static Builder|CreditPackPrice whereRecurring($value)
 * @method static Builder|CreditPackPrice whereStripePriceId($value)
 * @method static Builder|CreditPackPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CreditPackPrice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CreditPackPrice withoutTrashed()
 * @mixin Eloquent
 */
class CreditPackPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'stripe_price_id',
        'recurring',
        'price_in_pence',
    ];

    public function credit_pack(): BelongsTo
    {
        return $this->belongsTo(CreditPack::class);
    }

    public function gyms(): BelongsToMany
    {
        return $this->belongsToMany(Gym::class, '_pivot_credit_pack_price_gym');
    }
}
