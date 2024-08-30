<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IntensityMET
 *
 * @property int $id
 * @property string $intensity
 * @property float $met_value
 * @method static \Database\Factories\IntensityMETFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|IntensityMET newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntensityMET newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntensityMET query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntensityMET whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntensityMET whereIntensity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntensityMET whereMetValue($value)
 * @mixin \Eloquent
 */
class IntensityMET extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'intensity_METs';

    protected $hidden = [
        'pivot',
    ];

}
