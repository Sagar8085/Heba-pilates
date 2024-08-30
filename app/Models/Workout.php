<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Workout
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property string|null $image_path
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WorkoutCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $cooldown
 * @property-read int|null $cooldown_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $exercises
 * @property-read int|null $exercises_count
 * @property-read mixed $created_human
 * @property-read mixed $excerpt
 * @property-read mixed $image
 * @property-read String $price_human
 * @property-read mixed $purchase
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $training
 * @property-read int|null $training_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $warmup
 * @property-read int|null $warmup_count
 * @method static \Database\Factories\WorkoutFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workout newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workout onlyAdminWorkouts()
 * @method static \Illuminate\Database\Query\Builder|Workout onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Workout onlyUserWorkouts()
 * @method static \Illuminate\Database\Eloquent\Builder|Workout query()
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workout whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Workout withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Workout withoutTrashed()
 * @mixin \Eloquent
 */
class Workout extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'workouts';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_path',
        'processed',
        'created_by',
    ];

    protected $appends = [
        'created_human',
        'excerpt',
        'image',
        'price_human',
    ];

    /**
     * Get the Workout's order by polymorphic relation.
     */
    public function order()
    {
        return $this->morphOne(Order::class, 'orderable');
    }

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

    /**
     * Scope a query to only include workouts created by aan admin.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyAdminWorkouts($query)
    {
        return $query->join('users', 'users.id', 'workouts.created_by')->whereIn('users.role_id',
            [1, 2, 3])->select('workouts.*')->with(['categories', 'warmup', 'training', 'cooldown']);
    }

    /**
     * Scope a query to only include workouts created by a user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyUserWorkouts($query)
    {
        return $query->join('users', 'users.id', 'workouts.created_by')->where('users.role_id',
            4)->select('workouts.*')->with('categories');
    }


    public function categories()
    {
        return $this->belongsToMany('App\Models\WorkoutCategory', '_pivot_workout_category', 'workout_id',
            'category_id')->withTimestamps();
    }

    public function exercises()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_workout_exercises', 'workout_id',
            'exercise_id')->withPivot('workout_section_id', 'store_workout_type', 'custom_duration', 'custom_sets',
            'custom_reps', 'custom_rest');
    }

    // Split each exercise section to be able to pull each individually should it be required, warmup / training / cooldown.

    public function warmup()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_workout_exercises', 'workout_id',
            'exercise_id')->withPivot('workout_section_id', 'store_workout_type', 'custom_duration', 'custom_sets',
            'custom_reps', 'custom_rest')->withTimestamps()->wherePivot('workout_section_id', 1);
    }

    public function training()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_workout_exercises', 'workout_id',
            'exercise_id')->withPivot('workout_section_id', 'store_workout_type', 'custom_duration', 'custom_sets',
            'custom_reps', 'custom_rest')->withTimestamps()->wherePivot('workout_section_id', 2);
    }

    public function cooldown()
    {
        return $this->belongsToMany('App\Models\Exercise', '_pivot_workout_exercises', 'workout_id',
            'exercise_id')->withPivot('workout_section_id', 'store_workout_type', 'custom_duration', 'custom_sets',
            'custom_reps', 'custom_rest')->withTimestamps()->wherePivot('workout_section_id', 3);
    }

    // FOR CUSTOM WORKOUTS

    public function users()
    {
        return $this->belongsToMany('App\Models\User', '_pivot_custom_workout_users', 'workout_id',
            'user_id')->withTimestamps();
    }

    public function getPurchaseAttribute()
    {
        $user = User::loadFromBearer();

        if ($user !== null) {
            return Order::where('member_id', $user->id)
                ->where('orderable_id', $this->id)
                ->where('orderable_type', 'App\Models\Workout')
                ->first();
        }

        return null;
    }

    public function getPriceHumanAttribute(): string
    {
        /** @var \App\Models\User $user */
        $user = User::loadFromBearer();

        if ($user !== null && $user->hasActiveSubscription('premium')) {
            return 'PREMIUM';
        }

        if ($this->purchase !== null) {
            return 'PURCHASED';
        }

        if ($this->price == 0) {
            return 'FREE';
        }

        return '£' . number_format($this->price / 100, 2);
    }
}
