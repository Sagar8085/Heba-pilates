<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExerciseSection
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $cooldowns
 * @property-read int|null $cooldowns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $exercises
 * @property-read int|null $exercises_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $training
 * @property-read int|null $training_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $warmups
 * @property-read int|null $warmups_count
 * @method static \Database\Factories\ExerciseSectionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseSection whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExerciseSection extends Model
{
    use HasFactory;

    // gets all exercises
    public function exercises()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_exercise_sections', 'section_id',
            'exercise_id')->withTimestamps();
    }

    // Gets only warmup exercises
    public function warmups()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_exercise_sections', 'section_id',
            'exercise_id')->withTimestamps()->wherePivot('section_id', 1)->withPivot('id');
    }

    // Gets only training exercises
    public function training()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_exercise_sections', 'section_id',
            'exercise_id')->withTimestamps()->wherePivot('section_id', 2)->withPivot('id');
    }

    // Gets only cooldown exercises
    public function cooldowns()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_exercise_sections', 'section_id',
            'exercise_id')->withTimestamps()->wherePivot('section_id', 3)->withPivot('id');
    }
}
