<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExerciseStep
 *
 * @property int $id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ExerciseStepFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExerciseStep extends Model
{
    use HasFactory;

    protected $table = 'exercise_steps';

    protected $fillable = [
        'text',
    ];
}
