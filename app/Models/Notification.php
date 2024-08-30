<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property int $recipient_id
 * @property int $sender_id
 * @property int $object_id
 * @property string $type
 * @property string $message
 * @property int $read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Carbon $time_human
 * @property-read \App\Models\User|null $sender
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipient_id',
        'sender_id',
        'object_id',
        'type',
        'message',
        'read',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'time_human',
    ];

    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }

    public function getTimeHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('g:ia l dS F Y');
    }

    /**
     * Mark notification as read.
     */
    public function read()
    {
        $this->attributes['read'] = 1;
    }
}
