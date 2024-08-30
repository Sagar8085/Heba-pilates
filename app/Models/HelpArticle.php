<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HelpArticle
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $content
 * @property int $featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $category_id
 * @property-read \App\Models\HelpCategory|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle isFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HelpArticle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HelpArticle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'help_topics';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'name',
        'slug',
        'content',
        'category_id',
        'featured',
    ];

    public function category()
    {
        return $this->hasOne(HelpCategory::class, 'id', 'category_id');
    }

    public function scopeisFeatured($query)
    {
        return $query->where('featured', 1);
    }
}
