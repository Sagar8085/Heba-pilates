<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrainerZoomIds
 *
 * @property int $id
 * @property int $trainer_id
 * @property string $zoom_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds whereTrainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerZoomIds whereZoomUserId($value)
 * @mixin \Eloquent
 */
class TrainerZoomIds extends Model
{
    use HasFactory;

    protected $fillable = [
        'zoom_user_id',
        'trainer_id',
    ];
}
