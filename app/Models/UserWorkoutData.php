<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserWorkoutData
 *
 * @property int $id
 * @property int $user_id
 * @property int $workout_id
 * @property int $duration
 * @property int $kcals_burnt
 * @property int $num_exercises
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereKcalsBurnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereNumExercises($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkoutData whereWorkoutId($value)
 * @mixin \Eloquent
 */
class UserWorkoutData extends Model
{
    use HasFactory;

    protected $table = 'user_workout_data';

    protected $fillable = [
        'user_id',
        'workout_id',
        'duration',
        'kcals_burnt',
        'num_exercises',
    ];

}
