<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ContentPreference
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContentPreference whereUserId($value)
 * @mixin \Eloquent
 */
class ContentPreference extends Model
{
    use HasFactory;

    protected $table = 'user_content_preferences';

    protected $fillable = [
        'user_id',
        'category_id',
    ];
}
