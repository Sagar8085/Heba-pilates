<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveClassCategory
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read String $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LiveClass[] $upcomingClasses
 * @property-read int|null $upcoming_classes_count
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LiveClassCategory extends Model
{
    protected $table = 'live_classes_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image_path',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'image',
    ];

    public function getImageAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (\Str::contains($this->image_path, 'https://')) {
            return $this->image_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->image_path;
    }

    public function upcomingClasses()
    {
        return $this->hasMany('App\Models\LiveClass', 'category_id', 'id')->with('category',
            'equipment')->where('datetime', '>=', Carbon::now()->format('Y-m-d H:i:s'));
    }
}
