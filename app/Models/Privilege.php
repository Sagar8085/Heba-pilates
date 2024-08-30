<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Privilege
 *
 * @property int $id
 * @property int $user_id
 * @property string $privilege
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Privilege newModelQuery()
 * @method static Builder|Privilege newQuery()
 * @method static Builder|Privilege query()
 * @method static Builder|Privilege whereCreatedAt($value)
 * @method static Builder|Privilege whereId($value)
 * @method static Builder|Privilege wherePrivilege($value)
 * @method static Builder|Privilege whereUpdatedAt($value)
 * @method static Builder|Privilege whereUserId($value)
 * @mixin \Eloquent
 */
class Privilege extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'privilege',
    ];
}
