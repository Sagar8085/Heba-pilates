<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveClassRating
 *
 * @property int $id
 * @property int $liveclass_id
 * @property int $member_id
 * @property int $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating whereLiveclassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveClassRating whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LiveClassRating extends Model
{
    protected $table = 'live_classes_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'liveclass_id',
        'member_id',
        'rating',
    ];
}
