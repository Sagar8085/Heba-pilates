<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Contracts
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $expires
 * @property int|null $created_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $created_human
 * @property-read mixed $expiry_human
 * @property-read mixed $file
 * @property-read \App\Models\Order|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts newQuery()
 * @method static \Illuminate\Database\Query\Builder|Contracts onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contracts whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Contracts withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Contracts withoutTrashed()
 * @mixin \Eloquent
 */
class Contracts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = [
        'name',
        'path',
        'user_id',
        'created_by',
        'deleted_by',
        'expires',
    ];

    protected $appends = [
        'created_human',
        'expiry_human',
        'file',
    ];

    protected $casts = [
        'expires' => 'datetime',
    ];

    /**
     * Get the Workout's order by polymorphic relation.
     */
    public function order()
    {
        return $this->morphOne(Order::class, 'orderable');
    }

    public function getCreatedHumanAttribute()
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExpiryHumanAttribute()
    {
        if ($this->expires !== null) {
            return $this->expires->format('l dS F Y');
        }

        return 'No Expiry';

    }

    public function getFileAttribute()
    {
        if (\Str::contains($this->path, 'https://')) {
            return $this->path;
        }

        return getenv('AWS_BUCKET_URL') . '/' . $this->path;
    }


}
