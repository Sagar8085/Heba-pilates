<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OpeningTime
 *
 * @property int $id
 * @property int $gym_id
 * @property int $closed
 * @property string $date
 * @property string|null $opening_time
 * @property string|null $closing_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereClosingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereOpeningTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpeningTime whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OpeningTime extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'gym_id',
        'closed',
        'date',
        'opening_time',
        'closing_time',
    ];
}
