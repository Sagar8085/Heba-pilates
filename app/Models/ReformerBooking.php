<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Carbon\Carbon;
use Database\Factories\ReformerBookingFactory;
use DateTime;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\CalendarLinks\Link;

/**
 * App\Models\ReformerBooking
 *
 * @property int|null $id
 * @property int|null $user_id
 * @property int|null $reformer_id
 * @property string|null $datetime
 * @property int|null $bookable_id
 * @property string|null $bookable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read User|null $deleter
 * @property-read Carbon $created_at_human
 * @property-read mixed $date_formatted
 * @property-read Carbon $date_human
 * @property-read mixed $deleted_at_human
 * @property-read mixed $formatted_csv_date
 * @property-read Carbon $time_human
 * @property-read LiveClassBooking|null $liveclassBooking
 * @property-read User|null $member
 * @property-read Reformer|null $reformer
 * @method static ReformerBookingFactory factory(...$parameters)
 * @method static Builder|ReformerBooking filterAccess()
 * @method static Builder|ReformerBooking newModelQuery()
 * @method static Builder|ReformerBooking newQuery()
 * @method static Builder|ReformerBooking notBlocked()
 * @method static \Illuminate\Database\Query\Builder|ReformerBooking onlyTrashed()
 * @method static Builder|ReformerBooking query()
 * @method static Builder|ReformerBooking whereBookableId($value)
 * @method static Builder|ReformerBooking whereBookableType($value)
 * @method static Builder|ReformerBooking whereCreatedAt($value)
 * @method static Builder|ReformerBooking whereDatetime($value)
 * @method static Builder|ReformerBooking whereDeletedAt($value)
 * @method static Builder|ReformerBooking whereDeletedBy($value)
 * @method static Builder|ReformerBooking whereId($value)
 * @method static Builder|ReformerBooking whereReformerId($value)
 * @method static Builder|ReformerBooking whereUpdatedAt($value)
 * @method static Builder|ReformerBooking whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ReformerBooking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReformerBooking withoutTrashed()
 * @mixin Eloquent
 */
class ReformerBooking extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reformer_id',
        'datetime',
        'bookable_id',
        'bookable_type',
        'deleted_by',
    ];

    protected $appends = [
        'date_human',
        'time_human',
        'created_at_human',
        'date_formatted',
        'deleted_at_human',
        'formatted_csv_date',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($booking) {
            $user = User::where('id', $booking->user_id)->withTrashed()->first();

            if ($user !== null) {
                $user->guest_status = $user->getGuestStatus();
                $user->save();
            }
        });

        static::deleted(function ($booking) {
            $booking->sendBookingCancellationEmail();
        });
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('g:ia l dS F Y');
    }


    public function getDateHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['datetime'])->format('l dS F');
    }


    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->attributes['datetime'])->format('dS F Y - l');
    }


    public function getFormattedCsvDateAttribute()
    {
        return Carbon::parse($this->attributes['datetime'])->format('Y-m-d H:i:s l');
    }

    public function getTimeHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['datetime'])->format('H:i');
    }

    /**
     * Get the polymorphic relation.
     */
    public function liveclassBooking()
    {
        return $this->morphOne(LiveClassBooking::class, 'bookable');
    }

    public function reformer()
    {
        return $this->hasOne(Reformer::class, 'id', 'reformer_id');
    }

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function deleter()
    {
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }

    public function getDeletedAtHumanAttribute()
    {
        if ($this->deleted_at) {
            return Carbon::parse($this->deleted_at)->format('g:ia l dS F Y');
        }

        return null;
    }

    public function sendBookingConfirmationEmail()
    {
        $user = User::where('id', $this->user_id)->withTrashed()->first();

        $fromDate = Carbon::parse($this->datetime)->format('Y-m-d H:i');
        $toDate = Carbon::parse($this->datetime)->addHours(1)->format('Y-m-d H:i');

        $from = DateTime::createFromFormat('Y-m-d H:i', $fromDate);
        $to = DateTime::createFromFormat('Y-m-d H:i', $toDate);

        $link = Link::create('Heba Pilates Session', $from, $to)
            ->description('Test Description')
            ->address($this->reformer->gym->address);

        $address = $this->reformer->gym->address;

        $icsFrom = Carbon::parse($this->datetime)->format('Ymd\THis');
        $icsTo = Carbon::parse($this->datetime)->addHours(1)->format('Ymd\THis');

        $icsString = trim("BEGIN:VCALENDAR\nVERSION:2.0\nCALSCALE:GREGORIAN\nBEGIN:VEVENT\nSUMMARY:Heba Pilates Session\nDTSTART;TZID=Europe/London:$icsFrom\nDTEND;TZID=Europe/London:$icsTo\nLOCATION:$address\nDESCRIPTION: Heba Pilates Booking\nSTATUS:CONFIRMED\nSEQUENCE:3\nEND:VEVENT\nEND:VCALENDAR");

        $file = Storage::disk('s3')->put('calendars/' . $this->id . '-event.ics', $icsString, 'public');

        SendEmailJob::dispatch($user, 'emails.user.booking-confirmation', 'Heba Pilates Booking Confirmation', [
            'booking' => $this,
            'googleCal' => $link->google(),
            'appleCal' => env('AWS_BUCKET_URL') . '/calendars/' . $this->id . '-event.ics',
        ])->onQueue('account-notifications');
    }

    public function sendBookingCancellationEmail()
    {
        $user = User::where('id', $this->user_id)->withTrashed()->first();
        SendEmailJob::dispatch($user, 'emails.user.booking-cancellation', 'Heba Pilates Booking Cancellation',
            ['booking' => $this, 'user' => $user])->onQueue('account-notifications');
    }

    /**
     * Scope to filter query to only return Bookings that this admin is able to access.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFilterAccess($query)
    {
        $gymIds = auth()->user()->accessibleGymsArray();

        return $query->whereIn('reformers.gym_id', $gymIds);
    }

    public function scopeNotBlocked($query)
    {
        return $query->where('bookable_type', '<>', 'blocked_out');
    }
}
