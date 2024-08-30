<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveClassFavourite
 *
 * @property int $id
 * @property int $user_id
 * @property int $liveclass_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite whereLiveclassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassFavourite whereUserId($value)
 * @mixin \Eloquent
 */
class LiveClassFavourite extends Model
{
    protected $table = '_pivot_live_classes_favourites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'liveclass_id',
    ];
}
