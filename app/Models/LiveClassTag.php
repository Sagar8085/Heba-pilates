<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveClassTag
 *
 * @property int $id
 * @property int $liveclass_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag whereLiveclassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LiveClassTag extends Model
{
    protected $table = '_pivot_live_classes_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'liveclass_id',
    ];
}
