<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserPodcastData
 *
 * @property int $id
 * @property int $user_id
 * @property int $podcast_id
 * @property int $current_time
 * @property int $completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData whereCurrentTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData wherePodcastId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPodcastData whereUserId($value)
 * @mixin \Eloquent
 */
class UserPodcastData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'podcast_id',
        'curent_time',
        'completed',
    ];
}
