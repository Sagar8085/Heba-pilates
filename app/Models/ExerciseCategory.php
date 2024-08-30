<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ExerciseCategory
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $image_path
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $exercises
 * @property-read int|null $exercises_count
 * @property-read mixed $created_human
 * @property-read mixed $excerpt
 * @property-read mixed $image
 * @method static \Database\Factories\ExerciseCategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExerciseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ExerciseCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExerciseCategory withoutTrashed()
 * @mixin \Eloquent
 */
class ExerciseCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'exercise_categories';


    protected $fillable = [
        'name',
        'description',
        'slug',
        'image_path',
        'created_by',
    ];

    protected $appends = [
        'created_human',
        'excerpt',
        'image',
    ];

    public function getCreatedHumanAttribute()
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExcerptAttribute()
    {
        return substr($this->description, 0, 150) . '...';
    }

    public function getImageAttribute()
    {
        if (\Str::contains($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return getenv('AWS_BUCKET_URL') . '/' . $this->image_path;
    }

    public function exercises()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_exercise_categories', 'category_id',
            'exercise_id')->onlyProcessed();
    }
}
