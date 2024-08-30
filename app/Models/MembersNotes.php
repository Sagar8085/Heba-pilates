<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MembersNotes
 *
 * @property int $id
 * @property int $user_id
 * @property string $content
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read Carbon $created_at_human
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes query()
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembersNotes whereUserId($value)
 * @mixin \Eloquent
 */
class MembersNotes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'content',

    ];

    protected $appends = [
        'created_at_human',
    ];

    public function getCreatedAtHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('g:ia l dS F Y');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
}
