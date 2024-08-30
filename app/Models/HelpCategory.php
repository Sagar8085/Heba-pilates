<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\HelpCategory
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HelpArticle[] $articles
 * @property-read int|null $articles_count
 * @method static Builder|HelpCategory newModelQuery()
 * @method static Builder|HelpCategory newQuery()
 * @method static Builder|HelpCategory query()
 * @method static Builder|HelpCategory whereCreatedAt($value)
 * @method static Builder|HelpCategory whereId($value)
 * @method static Builder|HelpCategory whereName($value)
 * @method static Builder|HelpCategory whereSlug($value)
 * @method static Builder|HelpCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HelpCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'name',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(HelpArticle::class, 'category_id', 'id');
    }
}
