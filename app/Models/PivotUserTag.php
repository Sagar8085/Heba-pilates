<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PivotUserTag
 *
 * @property int $id
 * @property int $tag_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotUserTag whereUserId($value)
 * @mixin \Eloquent
 */
class PivotUserTag extends Model
{
    use HasFactory;

    protected $table = '_pivot_user_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'tag_id',
        'user_id',
    ];

    /**
     * This relationship returns all of the on demand videos assigned to this category.
     */
    public function tags()
    {
        return $this->hasMany(UserTag::class, 'id', 'tag_id');
    }
}
