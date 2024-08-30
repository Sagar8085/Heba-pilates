<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Course
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $price
 * @property int $release_frequency
 * @property string|null $benefits
 * @property string|null $description
 * @property string|null $image_path
 * @property string|null $video_path
 * @property string|null $guidance_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CourseEpisode[] $episodes
 * @property-read int|null $episodes_count
 * @property-read String $course_length
 * @property-read Carbon $created_human
 * @property-read String $excerpt
 * @property-read String $guidance
 * @property-read String $image
 * @property-read String $price_human
 * @property-read Void $purchase
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereGuidancePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereReleaseFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereVideoPath($value)
 * @mixin \Eloquent
 */
class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'release_frequency',
        'benefits',
        'description',
        'image_path',
        'video_path',
        'guidance_path',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'created_human',
        'excerpt',
        'guidance',
        'image',
        'course_length',
        'price_human',
    ];

    /**
     * This relationship returns all of the on episodes assigned to this course.
     */
    public function episodes()
    {
        return $this->hasMany(CourseEpisode::class, 'course_id', 'id');
    }

    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->description, 0, 75) . '...';
    }

    public function getGuidanceAttribute(): ?string
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (\Str::contains($this->guidance_path, 'https://')) {
            return $this->guidance_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->guidance_path;
    }

    public function getImageAttribute(): ?string
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (\Str::contains($this->image_path, 'https://')) {
            return $this->image_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->image_path;
    }

    public function getCourseLengthAttribute(): string
    {
        $latestEpisode = CourseEpisode::where('course_id', $this->id)->orderBy('episode_number', 'DESC')->first();

        if ($latestEpisode !== null) {
            $lastUnlockDate = Carbon::now()->addDays(($latestEpisode->episode_number) * $this->release_frequency);
            return $lastUnlockDate->diffInDays(Carbon::now()) . ' Days';
        }

        return '';
    }

    public function getPurchaseAttribute()
    {
        $user = User::loadFromBearer();

        if ($user !== null) {
            return Order::where('member_id', $user->id)
                ->where('orderable_id', $this->id)
                ->where('orderable_type', 'App\Models\Course')
                ->first();
        }

        return null;
    }

    public function getPriceHumanAttribute(): string
    {
        if ($this->purchase !== null) {
            return 'PURCHASED';
        }

        if ($this->price == 0) {
            return 'FREE';
        }

        return '£' . number_format($this->price / 100, 2);
    }
}
