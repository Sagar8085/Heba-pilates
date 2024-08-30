<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\LeadActivityLog
 *
 * @property int|null $id
 * @property int|null $agent_id
 * @property int|null $lead_id
 * @property int|null $activity_type
 * @property int|null $note_id
 * @property string|null $details
 * @property string|null $extra_details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $agent
 * @property-read mixed $created_human
 * @property-read LeadActivityType|null $type
 * @method static Builder|LeadActivityLog newModelQuery()
 * @method static Builder|LeadActivityLog newQuery()
 * @method static Builder|LeadActivityLog query()
 * @method static Builder|LeadActivityLog whereActivityType($value)
 * @method static Builder|LeadActivityLog whereAgentId($value)
 * @method static Builder|LeadActivityLog whereCreatedAt($value)
 * @method static Builder|LeadActivityLog whereDetails($value)
 * @method static Builder|LeadActivityLog whereExtraDetails($value)
 * @method static Builder|LeadActivityLog whereId($value)
 * @method static Builder|LeadActivityLog whereLeadId($value)
 * @method static Builder|LeadActivityLog whereNoteId($value)
 * @method static Builder|LeadActivityLog whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LeadActivityLog extends Model
{
    use HasFactory;

    protected $table = 'leads_activity_log';

    protected $fillable = [
        'agent_id',
        'lead_id',
        'details',
        'extra_details',
        'activity_type',
        'note_id',
    ];

    protected $appends = [
        'created_human',
    ];

    public function type(): HasOne
    {
        return $this->hasOne(LeadActivityType::class, 'id', 'activity_type');
    }

    public function agent(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }

    public function getCreatedHumanAttribute(): ?string
    {
        return $this->created_at?->format('g:ia l dS F Y');
    }
}
