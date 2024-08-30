<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PodcastCategory
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image_path
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Carbon $created_human
 * @property-read String $excerpt
 * @property-read String $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Podcast[] $podcasts
 * @property-read int|null $podcasts_count
 * @method static \Database\Factories\PodcastCategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PodcastCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PodcastCategory extends Model
{
    use HasFactory;

    protected $table = 'podcast_categories';

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

    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->description, 0, 75) . '...';
    }

    /**
     * This relationship returns all of the podcasts assigned to this category.
     */
    public function podcasts()
    {
        return $this->belongsToMany(Podcast::class, '_pivot_podcast_categories', 'category_id',
            'podcast_id')->onlyPublished();
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
}
