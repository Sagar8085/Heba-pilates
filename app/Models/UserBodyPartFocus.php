<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserBodyPartFocus
 *
 * @property int $id
 * @property int $user_id
 * @property int $focus_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus whereFocusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBodyPartFocus whereUserId($value)
 * @mixin \Eloquent
 */
class UserBodyPartFocus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '_pivot_user_body_part_focuses';

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
