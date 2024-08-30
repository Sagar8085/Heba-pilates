<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PivotAdminGym
 *
 * @property int $id
 * @property int $user_id
 * @property int $gym_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym query()
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PivotAdminGym whereUserId($value)
 * @mixin \Eloquent
 */
class PivotAdminGym extends Model
{
    protected $table = '_pivot_admin_gyms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'gym_id',
    ];
}
