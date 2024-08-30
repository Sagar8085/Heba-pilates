<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadCallSchedule
 *
 * @property int $id
 * @property int $agent_id
 * @property int $lead_id
 * @property string $datetime
 * @property string $outcome
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $datetime_human
 * @property-read mixed $time_passed
 * @property-read mixed $type
 * @property-read \App\Models\Lead|null $lead
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereOutcome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCallSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadCallSchedule extends Model
{
    use HasFactory;

    protected $table = 'leads_calls_schedule';

    protected $fillable = ['agent_id', 'lead_id', 'datetime', 'outcome'];

    protected $appends = ['datetime_human', 'time_passed', 'type'];

    public function getDatetimeHumanAttribute()
    {
        return Carbon::parse($this->attributes['datetime'])->format('dS F Y \a\t h:iA');
    }

    public function getTimePassedAttribute()
    {
        $datetime = Carbon::parse($this->attributes['datetime']);
        $now = Carbon::now();

        if ($now >= $datetime) {
            return true;
        }

        return false;
    }

    public function getTypeAttribute()
    {
        return 'Follow up call';
    }

    public function lead()
    {
        return $this->hasOne('App\Models\Lead', 'id', 'lead_id');
    }
}
