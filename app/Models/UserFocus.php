<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserFocus
 *
 * @property int $id
 * @property int $user_id
 * @property int $focus_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus whereFocusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFocus whereUserId($value)
 * @mixin \Eloquent
 */
class UserFocus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '_pivot_user_focuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'user_id',
        'focus_id',
    ];
}
