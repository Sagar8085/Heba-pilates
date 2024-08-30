<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\UserTag
 *
 * @method static create(array $data)
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            '_pivot_user_tags',
            'tag_id',
            'user_id'
        );
    }
}
