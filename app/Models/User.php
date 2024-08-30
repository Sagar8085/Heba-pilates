<?php

namespace App\Models;

use App\Collections\LeadCollection;
use App\Jobs\SendEmailJob;
use App\Traits\Filterable;
use App\Traits\Sortable;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use DB;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Stripe\PaymentMethod;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int|null $role_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone_number
 * @property string|null $gender
 * @property string|null $password
 * @property string|null $api_token
 * @property string|null $avatar_path
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property int|null $age
 * @property string|null $street_address
 * @property string|null $city
 * @property string|null $postcode
 * @property bool|null $has_rated_app
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property bool|null $is_sales_agent
 * @property string|null $guest_status
 * @property string|null $subscription_status
 * @property-read CreditPackPurchase|null $creditPackPurchase
 * @property-read Collection|CreditPackPurchase[] $creditPackPurchases
 * @property-read int|null $credit_pack_purchases_count
 * @property-read Member|null $details
 * @property-read Collection|ReformerBooking[] $expiredReformerBookings
 * @property-read int|null $expired_reformer_bookings_count
 * @property-read Collection|Focus[] $focuses
 * @property-read int|null $focuses_count
 * @property-read mixed $appointments_made
 * @property-read mixed $available_credits
 * @property-read mixed $avatar
 * @property-read mixed $calls_made
 * @property-read mixed $created_at_human
 * @property-read mixed $date_of_birth_csv
 * @property-read mixed $dob_human
 * @property-read mixed $gym
 * @property-read mixed $lifetime_value
 * @property-read string $name
 * @property-read string $name_email
 * @property-read mixed $next_session
 * @property-read mixed $privileges
 * @property-read mixed $signups_made
 * @property-read int $studio_credits_remaining
 * @property-read string $subscription_status_human
 * @property-read bool $superadmin
 * @property-read int $total_studio_visits
 * @property-read string $visits_per_week
 * @property-read Collection|Gym[] $gyms
 * @property-read int|null $gyms_count
 * @property-read LeadCall|null $lastActivityCall
 * @property-read Collection|ReformerBooking[] $lastMonthReformerBookings
 * @property-read int|null $last_month_reformer_bookings_count
 * @property-read LeadActivityLog|null $lastSentEmail
 * @property-read Collection|Notification[] $latestNotifications
 * @property-read int|null $latest_notifications_count
 * @property-read UserQRCode|null $latestStudioVisit
 * @property-read Lead|null $lead
 * @property-read Collection|LeadCall[] $leadActivityCalls
 * @property-read int|null $lead_activity_calls_count
 * @property-read Collection|LeadActivityLog[] $leadActivityLogs
 * @property-read int|null $lead_activity_logs_count
 * @property-read LeadCollection|Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read Member|null $memberProfile
 * @property-read Collection|MembersNotes[] $membersNotes
 * @property-read int|null $members_notes_count
 * @property-read ReformerBooking|null $mostRecentPastReformerBooking
 * @property-read ReformerBooking|null $nextReformerBooking
 * @property-read MarketingPreference|null $notificationPreferences
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Order|null $orders
 * @property-read Collection|Package[] $packages
 * @property-read int|null $packages_count
 * @property-read PARQ|null $parq
 * @property-read Collection|Session[] $pendingSessions
 * @property-read int|null $pending_sessions_count
 * @property-read Trainer|null $profile
 * @property-read ReformerBooking|null $recentReformerBooking
 * @property-read Collection|ReformerBooking[] $reformerBookings
 * @property-read int|null $reformer_bookings_count
 * @property-read Collection|LeadActivityLog[] $sentEmails
 * @property-read int|null $sent_emails_count
 * @property-read Subscription|null $subscription
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|UserTag[] $tags
 * @property-read int|null $tags_count
 * @property-read LeadTarget|null $targets
 * @property-read Trainer|null $trainer
 * @property-read Collection|TrainerSpecialisation[] $trainerSpecialisations
 * @property-read int|null $trainer_specialisations_count
 * @property-read ReformerBooking|null $upcomingReformerBooking
 * @property-read Collection|Workout[] $workoutFav
 * @property-read int|null $workout_fav_count
 * @property-read Collection|Workout[] $workoutStats
 * @property-read int|null $workout_stats_count
 * @property-read Collection|Workout[] $workouts
 * @property-read int|null $workouts_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User filter($filters)
 * @method static Builder|User filterAccess()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyAdmins()
 * @method static Builder|User onlyIdleCreditPackMembers()
 * @method static Builder|User onlyIdleSubscriptionMembers()
 * @method static Builder|User onlyMembers()
 * @method static Builder|User onlyMembersCredit()
 * @method static Builder|User onlyMembersSubscription()
 * @method static Builder|User onlyTrainers()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static Builder|User query()
 * @method static Builder|User sort($sortables)
 * @method static Builder|User whereAge($value)
 * @method static Builder|User whereApiToken($value)
 * @method static Builder|User whereAvatarPath($value)
 * @method static Builder|User whereCity($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDateOfBirth($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereDeletedBy($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereGuestStatus($value)
 * @method static Builder|User whereHasRatedApp($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsSalesAgent($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhoneNumber($value)
 * @method static Builder|User wherePostcode($value)
 * @method static Builder|User whereRoleId($value)
 * @method static Builder|User whereStreetAddress($value)
 * @method static Builder|User whereSubscriptionStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withAdditionalFields()
 * @method static Builder|User withAssignedLeads()
 * @method static Builder|User withRelationships()
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static Builder|User withUnassignedLeads()
 * @method static Builder|User withoutCurrentUser()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use Filterable;
    use Sortable;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'name_email',
        'avatar',
        'calls_made',
        'appointments_made',
        'signups_made',
        'superadmin',
        'dob_human',
        'gym',
        'created_at_human',
        'subscription_status_human',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role_id' => 'integer',
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'datetime',
        'has_rated_app' => 'bool',
        'is_sales_agent' => 'bool',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        if (app()->runningInConsole()) {
            return;
        }

        static::created(function ($user) {
            $user->api_token = self::generateUniqueApiToken();
            $user->save();

            /**
             * Create marketing preferences row.
             * The default value for these options is to enable them.
             */
            MarketingPreference::updateOrCreate(['member_id' => $user->id], [
                'member_id' => $user->id,
            ]);

            Member::updateOrCreate(['user_id' => $user->id]);

            if ($user->date_of_birth) {
                $user->update([
                    'age' => $user->date_of_birth->age,
                ]);
            }
        });
    }

    public function getGymAttribute()
    {
        return Gym::query()
            ->join('members', 'members.home_studio_id', 'gyms.id')
            ->where('members.user_id', $this->id)
            ->select('gyms.name')
            ->first();
    }

    public function gyms(): BelongsToMany
    {
        return $this->belongsToMany(
            Gym::class,
            '_pivot_admin_gyms',
            'user_id',
            'gym_id'
        );
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(
            Package::class,
            '_pivot_trainer_packages',
            'user_id',
            'package_id'
        )->withTimestamps();
    }

    public function workouts(): BelongsToMany
    {
        return $this->belongsToMany(
            Workout::class,
            '_pivot_custom_workout_users',
            'user_id',
            'workout_id'
        )->withTimestamps();
    }

    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(MarketingPreference::class, 'member_id', 'id');
    }

    public function pendingSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'member_id', 'id')
            ->with('trainer')
            ->where('status', 'confirmed')
            ->where('datetime', '<', now()->format('Y-m-d H:i:s'));
    }

    public function latestNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id', 'id')
            ->with('sender')
            ->latest()
            ->take(5);
    }

    public function focuses(): BelongsToMany
    {
        return $this->belongsToMany(
            Focus::class,
            '_pivot_user_focuses',
            'user_id',
            'focus_id'
        );
    }

    public function getPrivilegesAttribute()
    {
        return Privilege::where('user_id', $this->id)
            ->get()
            ->pluck('privilege')
            ->toArray();
    }

    public function getAvatarAttribute()
    {
        if (!$this->avatar_path) {
            return null;
        }

        /**
         * If the path already contains a domain, just return the string without formatting.
         */
        if (Str::contains($this->avatar_path, 'https://')) {
            return $this->avatar_path;
        }

        // Change this to fetch the bucket url on env config
        return 'https://s3.eu-west-2.amazonaws.com/prod.hebepilates/' . $this->avatar_path;
    }

    public static function generateUniqueApiToken(): string
    {
        return Str::limit(Str::random(150) . sha1(now()->getTimestamp()), 190);
    }

    private function generateResetPasswordToken()
    {
        return PasswordReset::create([
            'user_id' => $this->id,
            'token' => Str::random(150) . $this->id . time(),
            'expires' => now()->addDays(30),
        ]);
    }

    private function generateConfirmationToken()
    {
        return EmailVerificationTokens::create([
            'user_id' => $this->id,
            'token' => Str::random(150) . $this->id . time(),
        ]);
    }

    public function sendResetPasswordEmail()
    {
        $passwordReset = $this->generateResetPasswordToken();

        SendEmailJob::dispatch(
            $this,
            'emails.user.forgot-password',
            'Reset Your Password',
            ['token' => $passwordReset, 'user' => $this]
        )->onQueue('account-notifications');
    }

    public function sendWelcomeEmail()
    {
        SendEmailJob::dispatch(
            $this,
            'emails.user.welcome',
            'Welcome, ' . $this->first_name,
            ['user' => $this]
        )->onQueue('account-notifications');

        $this->sendConfirmationEmail();
    }

    public function sendConfirmationEmail()
    {
        $confirmUser = $this->generateConfirmationToken();

        SendEmailJob::dispatch(
            $this,
            'emails.user.confirmation-email',
            'Welcome, ' . $this->first_name . '. Confirm Account.',
            ['user' => $this, 'token' => $confirmUser]
        )->onQueue('account-notifications');
    }

    public function sendInvitationEmail()
    {
        $confirmUser = $this->generateConfirmationToken();

        SendEmailJob::dispatch(
            $this,
            'emails.user.invitation',
            'Your Invited To Join ' . env('APP_NAME'),
            ['user' => $this, 'token' => $confirmUser]
        )->onQueue('account-notifications');
    }

    public function sendBulletDigitalWelcomeEmail()
    {
        $confirmUser = $this->generateConfirmationToken();

        SendEmailJob::dispatch(
            $this,
            'emails.user.invitation-bullet-digital',
            'Finish Your Account Setup',
            ['user' => $this, 'token' => $confirmUser]
        )->onQueue('account-notifications');
    }

    public function setDateOfBirth()
    {
        $dateOfBirth = request('dob_year') . '-' . request('dob_month') . '-' . request('dob_day');

        $this->date_of_birth = $dateOfBirth;
        $this->save();
    }

    public function getNameAttribute(): string
    {
        return ucwords($this->first_name . ' ' . $this->last_name);
    }

    public function getNameEmailAttribute(): string
    {
        return ucwords($this->first_name . ' ' . $this->last_name . ' - ' . $this->email);
    }

    public function details(): HasOne
    {
        return $this->hasOne(Member::class)->with('purchasedPackages');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('expires', '>', now())
            ->with('order');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function parq(): HasOne
    {
        return $this->hasOne(PARQ::class);
    }

    public function scopeOnlyMembers(Builder $query): Builder
    {
        return $query->where('role_id', 4);
    }

    public function scopeOnlyMembersSubscription(Builder $query): Builder
    {
        return $query->join('subscriptions', 'users.id', 'subscriptions.user_id')
            ->where('expires', '>=', date('Y-m-d H:i:s'))
            ->groupBy('subscriptions.user_id');
    }

    public function scopeOnlyMembersCredit(Builder $query): Builder
    {
        return $query->join('credit_packs_purchases', 'users.id', 'credit_packs_purchases.user_id')
            ->where('expires', '>=', date('Y-m-d H:i:s'))
            ->groupBy('credit_packs_purchases.user_id');
    }

    public function scopeOnlyIdleSubscriptionMembers(Builder $query): Builder
    {
        return $query->leftJoin('subscriptions', function ($join) {
            $join->on('subscriptions.user_id', '=', 'users.id')
                ->on('subscriptions.id', '=',
                    DB::raw("(SELECT max(id) from subscriptions WHERE subscriptions.user_id = users.id ORDER BY subscriptions.id DESC)")
                );
        })->whereDate('subscriptions.expires', '<', now())->whereNull('subscriptions.deleted_at');
    }

    public function scopeOnlyIdleCreditPackMembers(Builder $query): Builder
    {
        return $query->leftJoin('credit_packs_purchases', function ($join) {
            $join->on('credit_packs_purchases.user_id', '=', 'users.id')
                ->on(
                    'credit_packs_purchases.id',
                    '=',
                    DB::raw("(SELECT max(id) from credit_packs_purchases WHERE credit_packs_purchases.user_id = users.id ORDER BY credit_packs_purchases.id DESC)")
                );
        })->whereDate('credit_packs_purchases.expires', '<', now())
            ->whereNull('credit_packs_purchases.deleted_at');
    }

    public function scopeOnlyTrainers(Builder $query): Builder
    {
        return $query->where('role_id', 3);
    }

    public function scopeOnlyAdmins(Builder $query): Builder
    {
        return $query->whereIn('role_id', [1, 2]);
    }

    public function memberProfile(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Trainer::class);
    }

    public function creditPackPurchase(): HasOne
    {
        return $this->hasOne(CreditPackPurchase::class)
            ->latest('expires')
            ->where('expires', '>', now());
    }

    public function creditPackPurchases(): HasMany
    {
        return $this->hasMany(CreditPackPurchase::class);
    }

    public function membersNotes(): HasMany
    {
        return $this->hasMany(MembersNotes::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            UserTag::class,
            '_pivot_user_tags',
            'user_id',
            'tag_id'
        );
    }

    public static function loadFromBearer()
    {
        return User::where(
            'api_token',
            Str::replaceFirst('Bearer ', '', request()->header('Authorization', ''))
        )->first();
    }

    public function workoutFav(): BelongsToMany
    {
        return $this->belongsToMany(
            Workout::class,
            '_pivot_user_fav_workouts',
            'user_id',
            'workout_id'
        )->withTimestamps();
    }

    public function trainerSpecialisations(): BelongsToMany
    {
        return $this->belongsToMany(
            TrainerSpecialisation::class,
            '_pivot_trainer_specialisations',
            'user_id',
            'trainer_specialisation_id'
        );
    }

    public function workoutStats(): BelongsToMany
    {
        return $this->belongsToMany(
            Workout::class,
            '_pivot_user_workout_stats',
            'user_id',
            'workout_id'
        )
            ->withPivot('kcals_burnt', 'durationSecs', 'num_exercises')
            ->withTimestamps();
    }

    public function hasActiveSubscription($tier = null): bool
    {
        return $this->subscriptions()
            ->where('expires', '>', now())
            ->when($tier, fn (Builder $query, $tier) => $query->where('tier', $tier))
            ->exists();
    }

    public function getCallsMadeAttribute()
    {
        return $this->leadActivityCalls->count();
    }

    public function getAppointmentsMadeAttribute()
    {
        if ($this->is_sales_agent === 1) {
            return LeadAppointment::where('agent_id', $this->id)->count();
        }

        return 0;
    }


    public function getSignupsMadeAttribute()
    {
        if ($this->is_sales_agent === 1) {
            return LeadSignup::where('agent_id', $this->id)->count();
        }

        return 0;
    }

    public function leadActivityLogs(): HasManyThrough
    {
        return $this->hasManyThrough(LeadActivityLog::class, Lead::class);
    }

    public function leadActivityCalls(): HasManyThrough
    {
        return $this->hasManyThrough(LeadCall::class, Lead::class);
    }

    public function lastActivityCall(): HasManyThrough
    {
        return $this->hasOneThrough(LeadCall::class, Lead::class)
            ->whereNotNull('datetime')
            ->latest('datetime');
    }

    public function sentEmails(): HasManyThrough
    {
        return $this->leadActivityLogs()->where('activity_type', 3);
    }

    public function lastSentEmail()
    {
        return $this->hasOneThrough(LeadActivityLog::class, Lead::class)
            ->where('activity_type', 3)
            ->latest();
    }

    public function targets(): HasOne
    {
        return $this->hasOne(LeadTarget::class, 'agent_id', 'id');
    }

    public function getSuperadminAttribute(): bool
    {
        return $this->role_id === 1;
    }

    public function updatePreferredStudio()
    {
        $member = Member::updateOrCreate(['user_id' => $this->id]);

        $queryResult = DB::table('reformer_bookings')
            ->selectRaw('gyms.id, gyms.name, count(gyms.id) as bookings')
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->join('gyms', 'gyms.id', 'reformers.gym_id')
            ->groupBy('gym_id')
            ->orderByDesc('bookings')
            ->where('reformer_bookings.user_id', $this->id)
            ->first();

        if ($queryResult) {
            $gym = Gym::find($queryResult->id);
            if ($gym) {
                $member->update(['preferred_studio_id' => $gym->id]);

                /**
                 * If the user doesnt have a home studio set, lets' automatically set it for them based on their preferred. */
                if ($member->home_studio_id === null) {
                    $member->update(['home_studio_id' => $gym->id]);
                }
            }
        }
    }

    public function getDobHumanAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->format('dS F Y');
        }

        return '';
    }

    public function reformerBookings(): HasMany
    {
        return $this->hasMany(ReformerBooking::class, 'user_id', 'id');
    }

    public function upcomingReformerBooking(): HasOne
    {
        return $this->hasOne(ReformerBooking::class, 'user_id', 'id')
            ->where('datetime', '>=', date('Y-m-d H:i:s'))
            ->orderBy('datetime', 'ASC');
    }

    public function recentReformerBooking(): HasOne
    {
        return $this->hasOne(ReformerBooking::class, 'user_id', 'id')
            ->orderBy('datetime', 'DESC');
    }

    public function mostRecentPastReformerBooking(): HasOne
    {
        return $this->hasOne(ReformerBooking::class, 'user_id', 'id')
            ->where('datetime', '<', now())
            ->orderBy('datetime', 'DESC');
    }

    public function latestStudioVisit(): HasOne
    {
        return $this->hasOne(UserQRCode::class, 'user_id', 'id')
            ->whereNotNull('scanned_at')
            ->orderBy('created_at', 'DESC');
    }

    public function getTotalStudioVisitsAttribute(): int
    {
        return $this->expiredReformerBookings->count();
    }

    public function expiredReformerBookings(): HasMany
    {
        return $this->reformerBookings()->where('datetime', '<', now());
    }

    public function lastMonthReformerBookings(): HasMany
    {
        return $this->reformerBookings()
            ->whereBetween('datetime', [
                now()->subDays(28),
                now(),
            ]);
    }

    public function getVisitsPerWeekAttribute(): string
    {
        return number_format($this->lastMonthReformerBookings->count() / 4, 2);
    }

    public function getNextSessionAttribute()
    {
        return $this->nextReformerBooking;
    }

    public function nextReformerBooking(): HasOne
    {
        return $this->hasOne(ReformerBooking::class)
            ->where('datetime', '>', now());
    }

    /**
     * @return int
     */
    public function getStudioCreditsRemainingAttribute(): int
    {
        return CreditPackPurchase::select('studio_credits')
            ->where('user_id', '=', $this->id)
            ->whereDate('expires', '>=', now())
            ->get()
            ->pluck('studio_credits')
            ->sum();
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class, 'user_id', 'id')
            ->orderBy('created_at', 'DESC');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function getCreatedAtHumanAttribute()
    {
        return optional($this->created_at)->format('Y-m-d H:i:s');
    }

    public function getGuestStatus(): string
    {
        $anySubscription = Subscription::where('user_id', $this->id)->first();
        $anyCreditPack = CreditPackPurchase::where('user_id', $this->id)->first();

        /** Prospect: A user that has not had a subscription or credit pack at any time. */
        if ($anySubscription === null && $anyCreditPack === null) {
            return 'Prospect';
        }

        /** Active Guest: A user that has credits available and has visited within the last 14 days. */
        $subCredits = $this->subscription ? $this->subscription->studio_credits : 0;
        $packCredits = CreditPackPurchase::where('user_id', $this->id)->where('expires', '>=',
            date('Y-m-d H:i:s'))->sum('studio_credits');
        $lastVisitWithin14 = ReformerBooking::where('user_id', $this->id)
            ->where('datetime', '<', date('Y-m-d H:i:s'))
            ->where('datetime', '>', now()->subDays(14))
            ->first();

        if (($subCredits > 0 || $packCredits > 0) && $lastVisitWithin14 !== null) {
            return 'Active Guest';
        }

        /** Idle Guest: A user that has credits available and has not visited within the last 14 days */
        if (($subCredits > 0 || $packCredits > 0) && $lastVisitWithin14 === null) {
            return 'Idle Guest';
        }

        /** Lapsed Guest: A user that has no more active credits available. */
        if ($subCredits == 0 && $packCredits == 0) {
            return 'Lapsed Guest';
        }

        return '';
    }

    public function getSubscriptionStatusHumanAttribute(): string
    {
        return ucwords(str_replace('-', ' ', $this->subscription_status));
    }

    public function getLifetimeValueAttribute()
    {
        return number_format(optional($this->orders)->sum('value') ?: 0, 2);
    }

    public function orders(): HasOne
    {
        return $this->hasOne(Order::class, 'member_id', 'id');
    }

    public function getAvailableCreditsAttribute()
    {
        $packCredits = CreditPackPurchase::where('expires', '>', now())
            ->where('user_id', $this->id)
            ->sum('studio_credits');

        $subscriptionCredits = Subscription::where('expires', '>', now())
            ->where('user_id', $this->id)
            ->sum('studio_credits');

        return ($packCredits + $subscriptionCredits);
    }

    public function getDateOfBirthCsvAttribute()
    {
        try {
            if ($this->date_of_birth) {
                return Carbon::parse($this->date_of_birth)->format('Y-m-d');
            }

            return '';
        } catch (Exception $error) {
            return '';
        }
    }

    public function accessibleGymsArray()
    {
        if ($this->superadmin) {
            return Gym::get()->pluck('id')->toArray();
        } else {
            return PivotAdminGym::where('user_id', auth()->user()->id)->get()->pluck('gym_id')->toArray();
        }
    }

    public function scopeFilterAccess(Builder $query): Builder
    {
        return $query->join('members as members_filter', 'users.id',
            'members_filter.user_id')
            ->whereIn('members_filter.home_studio_id', auth()->user()->accessibleGymsArray());
    }

    public function scopeWithUnassignedLeads(Builder $query): Builder
    {
        return $query->whereHas('leads', fn ($query) => $query->whereNull('assigned_to'));
    }

    public function scopeWithAssignedLeads(Builder $query): Builder
    {
        return $query->whereHas('leads', fn ($query) => $query->whereNotNull('assigned_to'));
    }

    public function scopeWithoutCurrentUser(Builder $query): Builder
    {
        return $query->when(auth()->id(), fn (Builder $query, int $id) => $query->where('users.id', '<>', $id));
    }

    public function scopeWithRelationships(Builder $query)
    {
        $query->with([
            'subscription',
            'recentReformerBooking',
            'mostRecentPastReformerBooking',
            'latestStudioVisit',
            'lead',
            'memberProfile',
            'creditPackPurchase.pack',
        ]);
    }

    public function scopeWithAdditionalFields(Builder $query)
    {
        $query->withCount('expiredReformerBookings as total_studio_visits')
            ->withSum('orders as total_spend', 'value')
            ->withSum('orders as expected_future_value', 'value');
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod)
    {
        DB::transaction(function () use ($paymentMethod) {
            StripePaymentMethod::where('user_id', $this->id)->update(['default' => 0]);
            StripePaymentMethod::create([
                'user_id' => $this->id,
                'payment_method' => $paymentMethod->id,
                'type' => $paymentMethod->type,
                'default' => 1,
            ]);
        });
    }
}
