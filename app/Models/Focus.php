<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Focus
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exercise[] $exercises
 * @property-read int|null $exercises_count
 * @method static \Database\Factories\FocusFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Focus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Focus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Focus query()
 * @method static \Illuminate\Database\Eloquent\Builder|Focus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Focus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Focus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Focus whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Focus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Focus extends Model
{
    use HasFactory;

    protected $table = 'focus';

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(
            Exercise::class,
            '_pivot_exercise_focus',
            'focus_id',
            'exercise_id'
        )->withTimestamps();
    }
}
