<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PushToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $os
 * @property string $device_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushToken whereUserId($value)
 * @mixin \Eloquent
 */
class PushToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'os',
        'device_id',
    ];
}
