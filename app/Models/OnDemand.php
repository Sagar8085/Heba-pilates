<?php

namespace App\Models;

use Database\Factories\OnDemandFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Str;

/**
 * App\Models\OnDemand
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int|null $duration
 * @property int $reformer
 * @property int $category_id
 * @property Order|null $order
 * @property string|null $video_path
 * @property string|null $image_path
 * @property int $processed
 * @property int $published
 * @property int $instructor_id
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|OnDemandCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read OnDemandCategory|null $category
 * @property-read Collection|Equipment[] $equipment
 * @property-read int|null $equipment_count
 * @property-read Collection|User[] $favourites
 * @property-read int|null $favourites_count
 * @property-read Carbon $created_human
 * @property-read String $excerpt
 * @property-read bool $favourited
 * @property-read String $image
 * @property-read String $price_human
 * @property-read mixed $purchase
 * @property-read String $video
 * @property-read User|null $instructor
 * @property-read OnDemandWatchProgress|null $playbackHistory
 * @method static OnDemandFactory factory(...$parameters)
 * @method static Builder|OnDemand filterAdminAccess()
 * @method static Builder|OnDemand filterDurationRange($range)
 * @method static Builder|OnDemand filterInstructor($instructorId)
 * @method static Builder|OnDemand filterTag($tag)
 * @method static Builder|OnDemand isProcessed()
 * @method static Builder|OnDemand isPublished()
 * @method static Builder|OnDemand newModelQuery()
 * @method static Builder|OnDemand newQuery()
 * @method static \Illuminate\Database\Query\Builder|OnDemand onlyTrashed()
 * @method static Builder|OnDemand query()
 * @method static Builder|OnDemand whereCategoryId($value)
 * @method static Builder|OnDemand whereCreatedAt($value)
 * @method static Builder|OnDemand whereCreatedBy($value)
 * @method static Builder|OnDemand whereDeletedAt($value)
 * @method static Builder|OnDemand whereDescription($value)
 * @method static Builder|OnDemand whereDuration($value)
 * @method static Builder|OnDemand whereId($value)
 * @method static Builder|OnDemand whereImagePath($value)
 * @method static Builder|OnDemand whereInstructorId($value)
 * @method static Builder|OnDemand whereName($value)
 * @method static Builder|OnDemand whereOrder($value)
 * @method static Builder|OnDemand wherePrice($value)
 * @method static Builder|OnDemand whereProcessed($value)
 * @method static Builder|OnDemand wherePublished($value)
 * @method static Builder|OnDemand whereReformer($value)
 * @method static Builder|OnDemand whereUpdatedAt($value)
 * @method static Builder|OnDemand whereVideoPath($value)
 * @method static \Illuminate\Database\Query\Builder|OnDemand withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OnDemand withoutTrashed()
 * @mixin Eloquent
 * @property-read bool $watched
 * @property-read Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static Builder|OnDemand filterTagBySlugs($slugs)
 */
class OnDemand extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'on_demand_videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'reformer',
        'category_id',
        'order',
        'video_path',
        'image_path',
        'processed',
        'published',
        'instructor_id',
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
        'price_human',
        'favourited',
        'watched',
    ];

    public function getWatchedAttribute(): bool
    {
        return !!optional($this->playbackHistory)->completed;
    }

    public function getFavouritedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        return !!$this->favourites()->find(auth()->id());
    }

    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, '_pivot_on_demand_favourites', 'ondemand_id');
    }

    public function order(): MorphOne
    {
        return $this->morphOne(Order::class, 'orderable');
    }

    public function playbackHistory(): HasOne
    {
        return $this->hasOne(OnDemandWatchProgress::class, 'ondemand_id', 'id')
            ->where('user_id', auth()->id());
    }

    public function category(): HasOne
    {
        return $this->hasOne(OnDemandCategory::class, 'id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, '_pivot_on_demand_tags', 'ondemand_id', 'tag_id');
    }

    public function getPurchaseAttribute()
    {
        $user = User::loadFromBearer();

        if ($user !== null) {
            return Order::where('member_id', $user->id)
                ->where('orderable_id', $this->id)
                ->where('orderable_type', 'App\Models\OnDemand')
                ->first();
        }

        return null;
    }

    public function getCreatedHumanAttribute(): ?string
    {
        if ($this->created_at) {
            return $this->created_at->format('g:ia l dS F');
        }

        return null;
    }

    public function getExcerptAttribute(): string
    {
        return substr($this->description, 0, 75) . '...';
    }

    public function instructor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'instructor_id');
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, '_pivot_on_demand_equipment', 'ondemand_id', 'equipment_id');
    }

    public function categories()
    {
        return $this->belongsToMany(OnDemandCategory::class, '_pivot_on_demand_categories', 'on_demand_id',
            'category_id');
    }

    public function scopeIsPublished($query)
    {
        return $query->where('published', '1');
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

        return getenv('AWS_VIDEO_BUCKET_URL') . '/' . $this->video_path;
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

    public function getDurationAttribute($value)
    {
        return ($value / 100);
    }

    public function storeView()
    {
        OnDemandView::create([
            'on_demand_id' => $this->id,
            'user_id' => auth()->user()->id,
        ]);
    }

    public function scopeFilterDurationRange($query, $range)
    {
        if ($range && $range !== '61') {
            $split = explode('-', $range);
            $start = ($split[0] * 100);
            $end = ($split[1] * 100);

            return $query->where('on_demand_videos.duration', '>=', $start)->where('on_demand_videos.duration', '<=',
                $end);
        }

        if ($range === '61') {
            return $query->where('on_demand_videos.duration', '>=', 6000);
        }
    }

    public function scopeFilterInstructor($query, $instructorId)
    {
        if ($instructorId) {
            return $query->where('on_demand_videos.instructor_id', $instructorId);
        }
    }

    public function scopeFilterTag($query, $tag)
    {
        if ($tag) {
            return $query->join('_pivot_on_demand_tags', '_pivot_on_demand_tags.ondemand_id',
                'on_demand_videos.id')->where('tag_id', $tag);
        }
    }

    public function scopeFilterTagBySlugs($query, $slugs)
    {
        if (!empty($slugs)) {
            collect($slugs)->each(fn ($slug) => $query->whereHas('tags', fn ($query) => $query->where('slug', $slug)));
        }

        return $query;
    }

    public function scopeIsProcessed($query)
    {
        return $query->where('processed', 1);
    }

    /**
     * Scope to filter query to only return Videos that this admin is able to access.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFilterAdminAccess($query)
    {
        $gymIds = auth()->user()->accessibleGymsArray();

        return $query->leftJoin('_pivot_on_demand_categories', '_pivot_on_demand_categories.on_demand_id',
            'on_demand_videos.id')
            ->leftJoin('_pivot_on_demand_category_gyms', '_pivot_on_demand_category_gyms.category_id',
                '_pivot_on_demand_categories.category_id')
            ->whereIn('_pivot_on_demand_category_gyms.gym_id', $gymIds)
            ->orWhereNull('_pivot_on_demand_category_gyms.gym_id')
            ->groupBy('on_demand_videos.id');
    }
}
