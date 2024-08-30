<?php

namespace App\Models;

use App\Collections\LeadCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Lead
 *
 * @property mixed $assigned
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone_number
 * @property string $date_of_birth
 * @property string $gender
 * @property string $status
 * @property string $temperature
 * @property int|null $assigned_to
 * @property string|null $assigned_at
 * @property string|null $source
 * @property string|null $gym_locations
 * @property string|null $heard_from
 * @property string|null $fitness_history
 * @property string|null $previous_gyms
 * @property string|null $last_exercise
 * @property string|null $upcoming_events
 * @property string|null $family_situation
 * @property string|null $sleep_pattern
 * @property string|null $fitness_activities
 * @property string|null $fitness_goal
 * @property int|null $gym_id
 * @property int $interested
 * @property int $subscribe_weekly
 * @property int $subscribe_monthly
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $activities
 * @property-read mixed $appointment
 * @property-read mixed $appointment_raw
 * @property-read mixed $calls_made
 * @property-read mixed $date_of_birth_human
 * @property-read mixed $datetime
 * @property-read mixed $datetime_human
 * @property-read mixed $fitness_goal_human
 * @property-read mixed $focuses
 * @property-read String $full_name
 * @property-read mixed $image_url
 * @property-read Boolean $overdue
 * @property-read String $overdue_by
 * @property-read mixed $profile_completion
 * @property-read String $time_human
 * @property-read \App\Models\LeadAppointment|null $leadAppointment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LeadCall[] $leadCalls
 * @property-read int|null $lead_calls_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LeadActivityLog[] $leadEmails
 * @property-read int|null $lead_emails_count
 * @property-read \App\Models\User|null $user
 * @method static LeadCollection|static[] all($columns = ['*'])
 * @method static \Database\Factories\LeadFactory factory(...$parameters)
 * @method static LeadCollection|static[] get($columns = ['*'])
 * @method static Builder|Lead newModelQuery()
 * @method static Builder|Lead newQuery()
 * @method static Builder|Lead query()
 * @method static Builder|Lead status($status)
 * @method static Builder|Lead whereAssignedAt($value)
 * @method static Builder|Lead whereAssignedTo($value)
 * @method static Builder|Lead whereCreatedAt($value)
 * @method static Builder|Lead whereDateOfBirth($value)
 * @method static Builder|Lead whereEmail($value)
 * @method static Builder|Lead whereFamilySituation($value)
 * @method static Builder|Lead whereFirstName($value)
 * @method static Builder|Lead whereFitnessActivities($value)
 * @method static Builder|Lead whereFitnessGoal($value)
 * @method static Builder|Lead whereFitnessHistory($value)
 * @method static Builder|Lead whereGender($value)
 * @method static Builder|Lead whereGymId($value)
 * @method static Builder|Lead whereGymLocations($value)
 * @method static Builder|Lead whereHeardFrom($value)
 * @method static Builder|Lead whereId($value)
 * @method static Builder|Lead whereInterested($value)
 * @method static Builder|Lead whereLastExercise($value)
 * @method static Builder|Lead whereLastName($value)
 * @method static Builder|Lead wherePhoneNumber($value)
 * @method static Builder|Lead wherePreviousGyms($value)
 * @method static Builder|Lead whereSleepPattern($value)
 * @method static Builder|Lead whereSource($value)
 * @method static Builder|Lead whereStatus($value)
 * @method static Builder|Lead whereSubscribeMonthly($value)
 * @method static Builder|Lead whereSubscribeWeekly($value)
 * @method static Builder|Lead whereTemperature($value)
 * @method static Builder|Lead whereUpcomingEvents($value)
 * @method static Builder|Lead whereUpdatedAt($value)
 * @method static Builder|Lead whereUserId($value)
 * @mixin \Eloquent
 */
class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth',
        'status',
        'assigned_to',
        'assigned_at',
        'interested',
        'fitness_goal',
        'source',
        'temperature',
        'subscribe_weekly',
        'subscribe_monthly',
        'gym_locations',
        'heard_from',
        'fitness_history',
        'previous_gyms',
        'last_exercise',
        'upcoming_events',
        'family_situation',
        'sleep_pattern',
        'fitness_activities',
        'created_at',
        'updated_at',
        'gym_id',
        'user_id',
    ];

    /**
     * The attributes that should be appended to all responses.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'time_human',
        'overdue',
        'overdue_by',
        'appointment',
        'appointmentRaw',
        'calls_made',
        'image_url',
        'date_of_birth_human',
        'activities',
        'profile_completion',
        'datetime',
        'datetime_human',
        'fitness_goal_human',
        'focuses',
    ];

    public function newCollection(array $models = []): LeadCollection
    {
        return new LeadCollection($models);
    }

    /**
     * Scope a query to only include leads with the provided status.
     *
     * @param Builder $query
     * @param $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, $status)
    {
        /**
         * If the status provided is 'overdue', these are new leads that been new for longer than 24 hours.
         */
        if ($status === 'overdue') {
            $threshold = now()->subHours(24);

            return $query->where('status', 'new')
                ->where('created_at', '<', $threshold);
        }

        return $query->where('status', $status);
    }

    public function leadCalls(): HasMany
    {
        return $this->hasMany(LeadCall::class);
    }

    public function leadEmails(): HasMany
    {
        return $this->hasMany(LeadActivityLog::class)->where('activity_type', 3);
    }

    public function leadAppointment(): HasOne
    {
        return $this->hasOne(LeadAppointment::class)
            ->where('datetime', '>', now())
            ->orderBy('datetime', 'ASC');
    }

    /**
     * Returns a boolean if the lead is overdue or not.
     *
     * @param None
     * @return Boolean
     */
    public function getOverdueAttribute()
    {
        $threshold = now()->subHours(24);

        if ($this->created_at < $threshold && $this->status === 'new') {
            return true;
        }

        return false;
    }

    /**
     * Returns a how many hours a lead is overdue by.
     *
     * @param None
     * @return String
     */
    public function getOverdueByAttribute()
    {
        $threshold = now()->subHours(24);

        if ($this->created_at < $threshold && $this->status === 'new') {

            $overdue_time = $this->created_at->subHours(24);

            return now()->diffInHours($overdue_time) . ' hours';
        }

        return false;
    }

    /**
     * Returns a users full name.
     *
     * @param None
     * @return String
     */
    public function getFullNameAttribute()
    {
        return ucwords($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the human readable created at attribute.
     *
     * @param None
     * @return String
     */
    public function getTimeHumanAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])
            ->format('g:ia l dS F');
    }

    public function getAppointmentAttribute()
    {
        $appointment = LeadAppointment::where('lead_id', $this->id)
            ->where('datetime', '>', now())
            ->orderBy('datetime', 'ASC')
            ->first();

        if ($appointment) {
            if ($appointment->show === 1) {
                return $appointment->outcome;
            }

            return $appointment->outcome ? $appointment->outcome : Carbon::parse($appointment->datetime)->format('Y-m-d H:i:s l');
        }

        return 'No Upcoming Appointment';
    }

    public function getAppointmentRawAttribute()
    {
        $appointment = LeadAppointment::where('lead_id', $this->id)
            ->where('datetime', '>', now())
            ->orderBy('datetime', 'ASC')
            ->first();

        if ($appointment) {
            // return $appointment->show;

            if ($appointment->show === 1) {
                return $appointment->outcome;
            } else {
                return $appointment->outcome ? $appointment->outcome : $appointment->datetime;
            }
        }

        return null;
    }

    /**
     * Get the count of calls made to this Lead
     */
    public function getCallsMadeAttribute()
    {
        return LeadCall::where('lead_id', $this->id)->count();
    }

    /**
     * Get an image containing initials for the Lead
     */
    public function getImageUrlAttribute()
    {

        $default = "https://www.somewhere.com/homestar.jpg";
        $size = 40;

        $firstName = $this->first_name;
        $lastName = $this->last_name;

        $grav_url = "https://eu.ui-avatars.com/api/?name=$firstName+$lastName&background=17B5C8&color=fff";

        return $grav_url;
    }


    /**
     * Get a formatted date of birth
     *
     */
    public function getDateOfBirthHumanAttribute()
    {
        return Carbon::parse($this->attributes['date_of_birth'])->format('dS F Y');
    }

    public function getDateOfBirthAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * Get the activities for this lead
     */
    public function getActivitiesAttribute()
    {
        // $startOfMonth = now()->startOfMonth();
        // $endOfMonth = now()->endOfMonth();


        // Limit this to 12 unless they click load more.

        $activityLog = collect();
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);

            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $activities = LeadActivityLog::where('lead_id', $this->id)
                ->where('created_at', '>=', $startOfMonth)
                ->where('created_at', '<=', $endOfMonth)
                ->with('type')
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->get();

            if (count($activities) === 0) {
                continue;
            }

            $activityLog->push([
                'date' => $month->format('F Y'),
                'logs' => $activities,
            ]);

        }

        return $activityLog;
    }

    public function getProfileCompletionAttribute()
    {
        // we dont want these fields to be counted in the percentage!
        $attributeBlacklist = [
            'id',
            'created_at',
            'updated_at',
            'assigned_to',
            'assigned_at',
            'gym_id',
            'appointment',
            'interested',
            'subscribe_weekly',
            'subscribe_monthly',
        ];

        // loop through the attributes of the model
        $totalFilled = 0;
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $attributeBlacklist)) {
                continue;
            }

            if (!$value) {
                continue;
            }

            $totalFilled++;
        }

        // We minus count of blacklist here so we don't count the fields we dont need in the percentage
        $totalAttributes = count($this->attributes) - count($attributeBlacklist);

        // Round up so we dont have a massive decimal
        return ceil(($totalFilled / $totalAttributes) * 100);
    }

    public function getFitnessGoalAttribute($value): array
    {
        return explode(',', $value);
    }

    public function setFitnessGoalAttribute($value)
    {
        $this->attributes['fitness_goal'] = is_array($value) ? implode(',', $value) : $value;
    }

    public function getFitnessGoalHumanAttribute()
    {
        $goals = Goal::whereIn('id', $this->fitness_goal)->get()->pluck('name')->toArray();
        return implode(', ', $goals);
    }

    public function getFocusesAttribute()
    {
        $focuses = UserFocus::where('user_id', $this->user_id)->get()->pluck('focus_id')->toArray();
        return $focuses;
    }


    // We do this little hack for the Day Planner, as the other tasks are sorted by datetime field
    public function getDatetimeAttribute()
    {
        return $this->attributes['created_at'];
    }

    public function getDatetimeHumanAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('dS F Y \a\t h:iA');
    }

    public function subscribeWeekly()
    {
        $this->subscribe_weekly = true;
    }

    public function subscribeMonthly()
    {
        $this->subscribe_monthly = true;
    }

    public function unsubscribe()
    {
        $this->subscribe_weekly = false;
        $this->subscribe_monthly = false;
    }

    /**
     * A lead could have an assigned relationship.
     *
     */
    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id')
            ->withDefault()
            ->withTrashed();
    }

    /**
     * @return bool
     */
    public function withoutConversion(): bool
    {
        return is_null($this->assigned_to);
    }

    /**
     * @return bool
     */
    public function convertedToSubscription(): bool
    {
        return $this->assigned_to
            && ($this->subscribe_weekly || $this->subscribe_monthly);
    }

    /**
     * @return bool
     */
    public function convertedToCreditPack(): bool
    {
        return $this->assigned_to && !$this->subscribe_weekly && !$this->subscribe_monthly;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
