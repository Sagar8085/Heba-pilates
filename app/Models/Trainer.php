<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Trainer
 *
 * @property int $id
 * @property int $user_id
 * @property string $biography
 * @property string|null $qualifications
 * @property string|null $image_path
 * @property string|null $video_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read String $avatar
 * @property-read String $video
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereBiography($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereQualifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainer whereVideoPath($value)
 * @mixin \Eloquent
 */
class Trainer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'biography',
        'qualifications',
        'image_path',
        'video_path',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'avatar',
        'video',
    ];

    public function getAvatarAttribute(): ?string
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

    public function getVideoAttribute(): ?string
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
