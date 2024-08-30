<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PivotTrainerPackage
 *
 * @property int $id
 * @property int $package_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotTrainerPackage whereUserId($value)
 * @mixin \Eloquent
 */
class PivotTrainerPackage extends Model
{
    protected $table = '_pivot_trainer_packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id',
        'user_id',
        'token',
    ];
}
