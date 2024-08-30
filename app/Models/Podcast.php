<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Podcast
 *
 * @property int $id
 * @property string $name
 * @property String $description
 * @property string|null $image_path
 * @property string|null $audio_path
 * @property int|null $duration
 * @property float|null $price
 * @property string $status
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PodcastCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read String $audio
 * @property-read \App\Models\Carbon $created_human
 * @property-read String $duration_human
 * @property-read String $excerpt
 * @property-read String $image
 * @property-read String $price_human
 * @property-read mixed $purchase
 * @property-read String $status_human
 * @property-read \App\Models\Order|null $order
 * @method static \Database\Factories\PodcastFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast onlyPublished()
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast query()
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereAudioPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Podcast whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Podcast extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'audio_path',
        'status',
        'price',
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
        'duration_human',
        'price_human',
        'status_human',
        'audio',
    ];

    /**
     * Get the podcasts's order by polymorphic relation.
     */
    public function order()
    {
        return $this->morphOne(Order::class, 'orderable');
    }

    public function getPurchaseAttribute()
    {
        $user = User::loadFromBearer();

        if ($user !== null) {
            return Order::where('member_id', $user->id)
                ->where('orderable_id', $this->id)
                ->where('orderable_type', 'App\Models\Podcast')
                ->first();
        }

        return null;
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\PodcastCategory', '_pivot_podcast_categories', 'podcast_id',
            'category_id');
    }

    public function scopeOnlyPublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getCreatedHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F');
    }

    public function getDescriptionAttribute($description): string
    {
        return nl2br($description);
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->description, 0, 75) . '...';
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
        return getenv('AWS_BUCKET_URL') . '/' . $this->image_path;
    }

    public function getAudioAttribute(): ?string
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (\Str::contains($this->audio_path, 'https://')) {
            return $this->audio_path;
        }

        // Change this to fetch the bucket url on env config
        return getenv('AWS_BUCKET_URL') . '/' . $this->audio_path;
    }

    public function getDurationHumanAttribute(): string
    {
        return round($this->duration / 60) . ' mins';
    }

    public function getPriceHumanAttribute(): string
    {
        /** @var \App\Models\User $user */
        $user = User::loadFromBearer();

        if ($user !== null && $user->hasActiveSubscription()) {
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

    public function getStatusHumanAttribute(): string
    {
        return ucwords($this->status);
    }
}
