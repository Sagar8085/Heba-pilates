<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserGoal
 *
 * @property int $id
 * @property int $user_id
 * @property int $goal_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal whereGoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGoal whereUserId($value)
 * @mixin \Eloquent
 */
class UserGoal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '_pivot_user_goals';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'user_id',
        'goal_id',
    ];
}
