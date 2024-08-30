<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class LeadAppointment
 *
 * @package App\Models
 * @property int $id
 * @property int $agent_id
 * @property int $lead_id
 * @property \Illuminate\Support\Carbon $datetime
 * @property int $duration
 * @property string $outcome
 * @property string $outcome_reason
 * @property int $note_id
 * @property int $gym_id
 * @property int $subscribe_weekly
 * @property int $subscribe_monthly
 * @property int $unsubscribe
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $show
 * @property string|null $guide
 * @property-read \App\Models\User|null $agent
 * @property-read mixed $created_human
 * @property-read mixed $datetime_human
 * @property-read mixed $time_passed
 * @property-read mixed $type
 * @property-read \App\Models\Gym|null $gym
 * @property-read \App\Models\Lead|null $lead
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereGuide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereOutcome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereOutcomeReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereSubscribeMonthly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereSubscribeWeekly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereUnsubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAppointment withAgent($id)
 * @mixin \Eloquent
 */
class LeadAppointment extends Model
{
    use HasFactory;

    protected $table = 'leads_appointments';

    protected $fillable = [
        'agent_id',
        'lead_id',
        'gym_id',
        'duration',
        'notes',
        'datetime',
        'followup_datetime',
        'show',
        'outcome',
        'outcome_reason',
        'note_id',
        'guide',
    ];

    protected $appends = [
        'created_human',
        'datetime_human',
        'time_passed',
        'type',
    ];

    protected $casts = [
        'datetime' => 'datetime',
    ];

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class, 'id', 'lead_id');
    }

    public function agent(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }

    public function gym(): HasOne
    {
        return $this->hasOne(Gym::class, 'id', 'gym_id');
    }

    public function getCreatedHumanAttribute()
    {
        return $this->created_at->format('dS F Y \a\t h:iA');
    }

    public function getDatetimeHumanAttribute()
    {
        return $this->datetime->format('dS F Y \a\t h:iA');
    }

    public function getTimePassedAttribute()
    {
        return now()->gte($this->datetime);
    }

    public function getTypeAttribute()
    {
        return 'Appointment';
    }

    /**
     * Scope a query to filter by specific agent or whole team.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAgent($query, $id)
    {
        if ($id !== 'team') {
            return $query->where('agent_id', $id);
        }
    }
}
