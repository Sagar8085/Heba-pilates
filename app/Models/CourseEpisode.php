<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * App\Models\CourseEpisode
 *
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property string $description
 * @property int $episode_number
 * @property string|null $image_path
 * @property string|null $video_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read Carbon $created_human
 * @property-read String $excerpt
 * @property-read String $image
 * @property-read String $unlock_date
 * @property-read mixed $unlocked
 * @property-read String $video
 * @method static Builder|CourseEpisode newModelQuery()
 * @method static Builder|CourseEpisode newQuery()
 * @method static Builder|CourseEpisode query()
 * @method static Builder|CourseEpisode whereCourseId($value)
 * @method static Builder|CourseEpisode whereCreatedAt($value)
 * @method static Builder|CourseEpisode whereDescription($value)
 * @method static Builder|CourseEpisode whereEpisodeNumber($value)
 * @method static Builder|CourseEpisode whereId($value)
 * @method static Builder|CourseEpisode whereImagePath($value)
 * @method static Builder|CourseEpisode whereName($value)
 * @method static Builder|CourseEpisode whereUpdatedAt($value)
 * @method static Builder|CourseEpisode whereVideoPath($value)
 * @mixin \Eloquent
 */
class CourseEpisode extends Model
{
    use HasFactory;

    protected $table = 'courses_episodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'name',
        'description',
        'episode_number',
        'image_path',
        'video_path',
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
        'video',
        'unlocked',
        'unlock_date',
    ];

    /**
     * This relationship returns the related course.
     */
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->description, 0, 75) . '...';
    }

    public function getImageAttribute(): ?string
    {
        if (Str::contains($this->image_path, 'https://')) {
            return $this->image_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->image_path;
    }

    public function getVideoAttribute(): ?string
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (Str::contains($this->video_path, 'https://')) {
            return $this->video_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->video_path;
    }

    public function getUnlockedAttribute()
    {
        if ($this->unlock_date > date('Y-m-d')) {
            return false;
        }

        return true;
    }

    public function getUnlockDateAttribute(): string
    {
        $user = User::loadFromBearer();

        if ($user !== null) {

            /**
             * Load the order for this course.
             */
            $order = Order::where('orderable_id', $this->course_id)
                ->where('orderable_type', 'App\Models\Course')
                ->where('member_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($order !== null) {
                $carbon = Carbon::parse($order->created_at)->addDays(($this->course->release_frequency * $this->episode_number))->format('Y-m-d');
                return $carbon;
            }

        }

        return 'X Days after purchase';
    }
}
