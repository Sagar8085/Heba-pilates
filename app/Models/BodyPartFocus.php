<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BodyPartFocus
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus query()
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BodyPartFocus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BodyPartFocus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'body_part_focuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'name',
    ];
}
