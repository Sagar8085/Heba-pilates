<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vlog
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $image_path
 * @property string|null $video_url
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Carbon $created_human
 * @property-read String $excerpt
 * @property-read String $image
 * @property-read String $video
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vlog whereVideoUrl($value)
 * @mixin \Eloquent
 */
class Vlog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_path',
        'video_url',
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
        'video',
    ];

    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->content, 0, 75) . '...';
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

    public function getVideoAttribute(): mixed
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (\Str::contains($this->video_path, 'https://')) {
            return $this->video_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->video_path;
    }
}
