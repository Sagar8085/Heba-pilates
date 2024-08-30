<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\WorkoutCategory
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
 * @property-read mixed $created_human
 * @property-read mixed $excerpt
 * @property-read mixed $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workout[] $workouts
 * @property-read int|null $workouts_count
 * @method static \Database\Factories\WorkoutCategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|WorkoutCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkoutCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|WorkoutCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WorkoutCategory withoutTrashed()
 * @mixin \Eloquent
 */
class WorkoutCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'image_path',
        'created_by',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
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

    public function workouts()
    {
        return $this->belongsToMany(Workout::class, '_pivot_workout_category', 'category_id',
            'workout_id')->withTimestamps();
    }

}
