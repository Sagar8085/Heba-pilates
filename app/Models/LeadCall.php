<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\LeadCall
 *
 * @property int|null $id
 * @property int|null $agent_id
 * @property int|null $lead_id
 * @property \Illuminate\Support\Carbon|null $datetime
 * @property string|null $outcome
 * @property int|null $note_id
 * @property int|null $subscribe_weekly
 * @property int|null $subscribe_monthly
 * @property int|null $unsubscribe
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $agent
 * @property-read string $created_human
 * @property-read string|null $datetime_human
 * @property-read LeadNote|null $note
 * @method static Builder|LeadCall newModelQuery()
 * @method static Builder|LeadCall newQuery()
 * @method static Builder|LeadCall query()
 * @method static Builder|LeadCall whereAgentId($value)
 * @method static Builder|LeadCall whereCreatedAt($value)
 * @method static Builder|LeadCall whereDatetime($value)
 * @method static Builder|LeadCall whereId($value)
 * @method static Builder|LeadCall whereLeadId($value)
 * @method static Builder|LeadCall whereNoteId($value)
 * @method static Builder|LeadCall whereOutcome($value)
 * @method static Builder|LeadCall whereSubscribeMonthly($value)
 * @method static Builder|LeadCall whereSubscribeWeekly($value)
 * @method static Builder|LeadCall whereUnsubscribe($value)
 * @method static Builder|LeadCall whereUpdatedAt($value)
 * @method static Builder|LeadCall withAgent($id)
 * @mixin Eloquent
 */
class LeadCall extends Model
{
    use HasFactory;

    protected $table = 'leads_calls';

    protected $fillable = [
        'agent_id',
        'lead_id',
        'datetime',
        'outcome',
        'subscribe_weekly',
        'subscribe_monthly',
        'unsubscribe',
        'note_id',
    ];

    protected $appends = [
        'created_human',
        'datetime_human',
    ];

    protected $casts = [
        'datetime' => 'datetime',
    ];

    public function agent(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }

    public function note(): HasOne
    {
        return $this->hasOne(LeadNote::class, 'id', 'note_id');
    }

    public function getCreatedHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('dS F Y \a\t h:iA');
    }

    public function getDatetimeHumanAttribute(): ?string
    {
        if (!$this->attributes['datetime']) {
            return null;
        }

        return Carbon::parse($this->attributes['datetime'])->format('dS F Y \a\t h:iA');
    }

    /**
     * Scope a query to filter by specific agent or whole team.
     *
     * @param Builder $query
     * @param $id
     *
     * @return Builder
     */
    public function scopeWithAgent($query, $id)
    {
        if ($id !== 'team') {
            return $query->where('agent_id', $id);
        }

        return $query;
    }
}
