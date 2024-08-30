<?php

namespace App\Models;

use Database\Factories\OnDemandCategoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OnDemandCategory
 *
 * @property int|null $id
 * @property string|null $name
 * @property string|null $slug
 * @property int|null $order
 * @property string|null $description
 * @property string|null $image_path
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $created_human
 * @property-read String $excerpt
 * @property-read String $image
 * @property-read Collection|Gym[] $gyms
 * @property-read int|null $gyms_count
 * @property-read Collection|OnDemand[] $videos
 * @property-read int|null $videos_count
 * @method static OnDemandCategoryFactory factory(...$parameters)
 * @method static Builder|OnDemandCategory filterAdminAccess()
 * @method static Builder|OnDemandCategory filterMemberAccess()
 * @method static Builder|OnDemandCategory newModelQuery()
 * @method static Builder|OnDemandCategory newQuery()
 * @method static Builder|OnDemandCategory query()
 * @method static Builder|OnDemandCategory whereCreatedAt($value)
 * @method static Builder|OnDemandCategory whereCreatedBy($value)
 * @method static Builder|OnDemandCategory whereDescription($value)
 * @method static Builder|OnDemandCategory whereId($value)
 * @method static Builder|OnDemandCategory whereImagePath($value)
 * @method static Builder|OnDemandCategory whereName($value)
 * @method static Builder|OnDemandCategory whereOrder($value)
 * @method static Builder|OnDemandCategory whereSlug($value)
 * @method static Builder|OnDemandCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OnDemandCategory extends Model
{
    use HasFactory;

    protected $table = 'on_demand_categories';

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
        'order',
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
     * This relationship returns all of the on demand videos assigned to this category.
     */
    public function videos()
    {
        return $this->belongsToMany(OnDemand::class, '_pivot_on_demand_categories', 'category_id', 'on_demand_id')
            ->isProcessed()
            ->isPublished()
            ->with('equipment', 'instructor')
            ->filterDurationRange(request('duration'))
            ->filterInstructor(request('instructor'))
            ->filterTag(request('tag'))
            ->filterTagBySlugs(request('tags'))
            // ->filterKeyword(request('keyword'))
            ->orderBy('_pivot_on_demand_categories.order', 'ASC');
    }

    /**
     * This relationship returns all of the gyms this category is assigned to.
     *
     * @param None
     *
     * @return App\Models\Gym
     */
    public function gyms()
    {
        return $this->belongsToMany(Gym::class, '_pivot_on_demand_category_gyms', 'category_id', 'gym_id');
    }

    public function getImageAttribute(): ?string
    {
        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (\Str::contains($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return getenv('AWS_BUCKET_URL') . '/' . $this->image_path;
    }

    /**
     * Scope to filter query to only return Categories that this admin is able to access.
     *
     * @param $query
     *
     * @return Builder
     */
    public function scopeFilterAdminAccess($query): Builder
    {
        /** @var User $user */
        $user = auth()->user();
        $gymIds = $user?->accessibleGymsArray() ?? [];

        $query = $query->groupBy('on_demand_categories.id');

        if ($user?->superadmin) {
            // Superadmins should see everything, otherwise non-assigned
            // categories will never be shown to anyone.
            return $query;
        }

        return $query
            ->join(
                '_pivot_on_demand_category_gyms',
                '_pivot_on_demand_category_gyms.category_id',
                '=',
                'on_demand_categories.id',
            )
            ->whereIn(
                '_pivot_on_demand_category_gyms.gym_id',
                $gymIds,
            );
    }

    /**
     * Scope to filter query to only return Categories that this member is able to access.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFilterMemberAccess($query)
    {
        return $query->join('_pivot_on_demand_categories', '_pivot_on_demand_categories.category_id',
            'on_demand_categories.id')
            ->join('_pivot_on_demand_category_gyms', '_pivot_on_demand_category_gyms.category_id',
                '_pivot_on_demand_categories.category_id')
            ->where('_pivot_on_demand_category_gyms.gym_id', auth()->user()->memberProfile->home_studio_id)
            ->groupBy('on_demand_categories.id');
    }
}
