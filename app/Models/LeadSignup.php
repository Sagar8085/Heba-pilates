<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadSignup
 *
 * @property int $id
 * @property int $agent_id
 * @property int $lead_id
 * @property int $member_id
 * @property string $contract_path
 * @property string $date_of_birth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $agent
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereContractPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadSignup withAgent($id)
 * @mixin \Eloquent
 */
class LeadSignup extends Model
{
    use HasFactory;

    protected $table = 'leads_signups';

    protected $fillable = ['agent_id', 'lead_id', 'member_id', 'contract_path', 'date_of_birth'];

    public function agent()
    {
        return $this->hasOne('App\Models\User', 'id', 'agent_id');
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
