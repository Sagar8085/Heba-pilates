<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $message
 * @property int $user_id
 * @property int $object_id
 * @property string $object_type
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $author
 * @property-read Carbon $created_human
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereCreatedBy($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereMessage($value)
 * @method static Builder|Event whereObjectId($value)
 * @method static Builder|Event whereObjectType($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event whereUserId($value)
 * @mixin \Eloquent
 */
class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'user_id',
        'object_id',
        'object_type',
        'created_by',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'created_human',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getCreatedHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('g:ia l dS F Y');
    }

    public static function createForModel(string $message, Model $model, $userId = null)
    {
        return static::create([
            'message' => $message,
            'user_id' => $userId ?? auth()->id(),
            'object_id' => $model->id,
            'object_type' => $model::class,
            'created_by' => auth()->id(),
        ]);
    }
}
