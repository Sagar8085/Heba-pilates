<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadNote
 *
 * @property int $id
 * @property int $agent_id
 * @property int $lead_id
 * @property string $type
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $agent
 * @property-read mixed $created_human
 * @property-read \App\Models\Lead|null $lead
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadNote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadNote extends Model
{
    use HasFactory;

    protected $table = 'leads_notes';

    protected $fillable = ['agent_id', 'lead_id', 'type', 'content'];

    protected $appends = ['created_human'];

    public function lead()
    {
        return $this->hasOne('App\Models\Lead');
    }

    public function agent()
    {
        return $this->hasOne('App\Models\User', 'id', 'agent_id');
    }

    public function getCreatedHumanAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('dS F Y \a\t h:iA');
    }
}
