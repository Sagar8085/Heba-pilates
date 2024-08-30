<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadTarget
 *
 * @property int $id
 * @property int $agent_id
 * @property int $calls
 * @property int $appointments
 * @property int $signups
 * @property int $close_ratio
 * @property int $leads
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Integer $appointments_monthly
 * @property-read Integer $appointments_weekly
 * @property-read Integer $appointments_yearly
 * @property-read Integer $calls_monthly
 * @property-read Integer $calls_weekly
 * @property-read Integer $calls_yearly
 * @property-read Integer $signups_monthly
 * @property-read Integer $signups_weekly
 * @property-read Integer $signups_yearly
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereAppointments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereCloseRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereSignups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTarget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadTarget extends Model
{
    use HasFactory;

    protected $table = 'leads_targets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_id',
        'calls',
        'appointments',
        'signups',
        'close_ratio',
    ];

    /**
     * The attributes that should be appended to all responses.
     *
     * @var array
     */
    protected $appends = [
        'calls_weekly',
        'calls_monthly',
        'calls_yearly',
        'appointments_weekly',
        'appointments_monthly',
        'appointments_yearly',
        'signups_weekly',
        'signups_monthly',
        'signups_yearly',
    ];

    public function getCallsWeeklyAttribute(): float
    {
        return round($this->calls * 7);
    }

    public function getCallsMonthlyAttribute(): float
    {
        return round($this->calls_weekly * 4);
    }

    public function getCallsYearlyAttribute(): float
    {
        return round($this->calls_monthly * 12);
    }

    public function getAppointmentsWeeklyAttribute(): float
    {
        return round($this->appointments * 7);
    }

    public function getAppointmentsMonthlyAttribute(): float
    {
        return round($this->appointments_weekly * 4);
    }

    public function getAppointmentsYearlyAttribute(): float
    {
        return round($this->appointments_monthly * 12);
    }

    public function getSignupsWeeklyAttribute(): float
    {
        return round($this->signups * 7);
    }

    public function getSignupsMonthlyAttribute()
    {
        return round($this->signups_weekly * 4);
    }

    public function getSignupsYearlyAttribute(): float
    {
        return round($this->signups_monthly * 12);
    }
}
