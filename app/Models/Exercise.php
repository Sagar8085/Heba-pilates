<?php

namespace App\Models;

use Database\Factories\ExerciseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Str;


/**
 * App\Models\Exercise
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $duration
 * @property int $duration_per_rep
 * @property string|null $video_path
 * @property string|null $image_path
 * @property int $processed
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $paid
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExerciseCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExerciseSection[] $cooldownSection
 * @property-read int|null $cooldown_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Focus[] $focus
 * @property-read int|null $focus_count
 * @property-read String $created_human
 * @property-read String $excerpt
 * @property-read String $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\IntensityMET[] $intensity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExerciseStep[] $steps
 * @property-read String $video
 * @property-read int|null $intensity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExerciseSection[] $sections
 * @property-read int|null $sections_count
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExerciseSection[] $trainingSection
 * @property-read int|null $training_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExerciseSection[] $warmupSection
 * @property-read int|null $warmup_section_count
 * @method static ExerciseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise onlyProcessed()
 * @method static Builder|Exercise onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise onlyUnpaid()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereDurationPerRep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereVideoPath($value)
 * @method static Builder|Exercise withTrashed()
 * @method static Builder|Exercise withoutTrashed()
 * @mixin \Eloquent
 */
class Exercise extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'exercises';

    protected $fillable = [
        'name',
        'description',
        'duration',
        'duration_per_rep',
        'processed',
        'created_by',
    ];

    protected $appends = [
        'created_human',
        'excerpt',
        'intensity',
        'image',
        'video',
        'steps',
    ];

    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->description, 0, 150) . '...';
    }

    public function getIntensityAttribute()
    {
        return $this->intensity()->get()->first();
    }

    public function getStepsAttribute()
    {
        return $this->steps()->get();
    }

    public function getImageAttribute(): ?string
    {
        if (Str::contains($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return env('AWS_BUCKET_URL') . '/' . $this->image_path;
    }

    public function getVideoAttribute(): ?string
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (Str::contains($this->video_path, 'https://')) {
            return $this->video_path;
        }

        return env('AWS_VIDEO_BUCKET_URL') . '/' . $this->video_path;
    }

    public function scopeOnlyProcessed($query)
    {
        return $query->where('processed', 1);
    }

    public function scopeOnlyUnpaid($query)
    {
        return $query->where('paid', 0);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            ExerciseCategory::class,
            '_pivot_exercise_categories',
            'exercise_id',
            'category_id'
        )->withTimestamps();
    }

    public function sections()
    {
        return $this->belongsToMany(ExerciseSection::class, '_pivot_exercise_sections', 'exercise_id',
            'section_id')->withPivot('id')->withTimestamps();
    }

    public function warmupSection(): BelongsToMany
    {
        return $this->belongsToMany(
            ExerciseSection::class,
            '_pivot_exercise_sections',
            'exercise_id',
            'section_id'
        )
            ->where('section_id', 1)
            ->withPivot('id');
    }

    public function trainingSection(): BelongsToMany
    {
        return $this->belongsToMany(
            ExerciseSection::class,
            '_pivot_exercise_sections',
            'exercise_id',
            'section_id'
        )
            ->where('section_id', 2)
            ->withPivot('id');
    }

    public function cooldownSection(): BelongsToMany
    {
        return $this->belongsToMany(
            ExerciseSection::class,
            '_pivot_exercise_sections',
            'exercise_id',
            'section_id'
        )
            ->where('section_id', 3)
            ->withPivot('id');
    }

    public function focus(): BelongsToMany
    {
        return $this->belongsToMany(
            Focus::class,
            '_pivot_exercise_focus',
            'exercise_id',
            'focus_id'
        )
            ->withTimestamps();
    }

    public function intensity(): BelongsToMany
    {
        return $this->belongsToMany(
            IntensityMET::class,
            '_pivot_exercise_intensity',
            'exercise_id',
            'intensity_id'
        )->withTimestamps();
    }

    public function steps(): BelongsToMany
    {
        return $this->belongsToMany(
            ExerciseStep::class,
            '_pivot_exercise_exercise_steps',
            'exercise_id',
            'exercise_step_id'
        )->withTimestamps();
    }


}
