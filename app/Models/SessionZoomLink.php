<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SessionZoomLink
 *
 * @property int $id
 * @property int $session_id
 * @property string $link
 * @property string $expires
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionZoomLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SessionZoomLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'link',
    ];
}
