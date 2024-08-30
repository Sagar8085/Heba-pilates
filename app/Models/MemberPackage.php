<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MemberPackage
 *
 * @property int $id
 * @property int $member_id
 * @property int $trainer_id
 * @property int $package_id
 * @property string|null $expires
 * @property int|null $total
 * @property int|null $remaining
 * @property int|null $length
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $active
 * @property-read Carbon $created_human
 * @property-read mixed $credits
 * @property-read mixed $days_until_expiry
 * @property-read String $expires_human
 * @property-read mixed $name
 * @property-read mixed $session_length
 * @property-read mixed $status
 * @property-read mixed $status_human
 * @property-read mixed $trainer_name
 * @property-read \App\Models\Package|null $package
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Session[] $sessions
 * @property-read int|null $sessions_count
 * @property-read \App\Models\User|null $trainer
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage createdMonth($year, $month)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereTrainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MemberPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MemberPackage extends Model
{
    protected $table = 'members_packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'trainer_id',
        'package_id',
        'expires',
        'total',
        'remaining',
        'length',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'created_human',
        'name',
        'credits',
        'active',
        'status',
        'session_length',
        'expires_human',
        'days_until_expiry',
        'status_human',
        'trainer_name',
    ];

    public function trainer()
    {
        return $this->hasOne('App\Models\User', 'id', 'trainer_id');
    }

    public function package()
    {
        return $this->hasOne('App\Models\Package', 'id', 'package_id');
    }

    public function sessions()
    {
        return $this->hasMany('App\Models\Session', 'user_package_id', 'id');
    }

    public function getCreatedHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('g:ia l dS F Y');
    }

    public function getDaysUntilExpiryAttribute()
    {
        $cdate = strtotime($this->expires);
        $today = time();
        $difference = $cdate - $today;
        if ($difference < 0) {
            $difference = 0;
        }
        return floor($difference / 60 / 60 / 24);
    }

    public function getTrainerNameAttribute()
    {
        $trainer = User::where('id', $this->trainer_id)->first();
        return $trainer->name;
    }

    public function getStatusHumanAttribute()
    {
        if (($this->remaining == 0) || ($this->expires < Date('Y-m-d H:i:s', time()))) {
            return 'Inactive';
        }
        return 'Active';
    }

    public function getNameAttribute()
    {
        return $this->package->name;
    }

    public function getCreditsAttribute()
    {
        return $this->remaining;
    }

    public function getActiveAttribute()
    {
        if ($this->remaining == 0) {
            return false;
        }

        return true;
    }

    public function deductCredit()
    {
        $this->update([
            'remaining' => ($this->remaining - 1),
        ]);
    }

    public function getStatusAttribute()
    {
        if ($this->remaining == 0) {
            return 'completed';
        }

        return 'active';
    }

    public function getSessionLengthAttribute()
    {
        return $this->length;
    }

    public function scopeCreatedMonth($query, $year, $month)
    {
        return $query->whereYear('members_packages.created_at', $year)->whereMonth('members_packages.created_at',
            $month);
    }

    public function getExpiresHumanAttribute(): string
    {
        if ($this->expires) {
            return Carbon::parse($this->expires)->format('g:ia l dS F Y');
        }

        return 'No Expiry Date';
    }
}
