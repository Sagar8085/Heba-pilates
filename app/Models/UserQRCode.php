<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QRCodeProvider;

/**
 * App\Models\UserQRCode
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $gym_id
 * @property string $identifier
 * @property string|null $expires_at
 * @property string|null $scanned_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Json $image
 * @property-read Carbon $scanned_at_human
 * @property-read \App\Models\Gym|null $gym
 * @property-read \App\Models\User|null $user
 * @method static Builder|UserQRCode isActive()
 * @method static Builder|UserQRCode isAuthUser()
 * @method static Builder|UserQRCode newModelQuery()
 * @method static Builder|UserQRCode newQuery()
 * @method static Builder|UserQRCode query()
 * @method static Builder|UserQRCode whereCreatedAt($value)
 * @method static Builder|UserQRCode whereExpiresAt($value)
 * @method static Builder|UserQRCode whereGymId($value)
 * @method static Builder|UserQRCode whereId($value)
 * @method static Builder|UserQRCode whereIdentifier($value)
 * @method static Builder|UserQRCode whereScannedAt($value)
 * @method static Builder|UserQRCode whereUpdatedAt($value)
 * @method static Builder|UserQRCode whereUserId($value)
 * @mixin \Eloquent
 */
class UserQRCode extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_qr_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'user_id',
        'gym_id',
        'identifier',
        'expires_at',
        'scanned_at',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var Array
     */
    protected $appends = [
        'image',
        'scanned_at_human',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function gym()
    {
        return $this->hasOne('App\Models\Gym', 'id', 'gym_id');
    }

    public function scopeIsAuthUser($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }

    public function scopeIsActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now())->whereNull('scanned_at');
    }

    public static function generate($userId)
    {
        $qrCode = UserQRCode::isAuthUser()->isActive()->latest()->first();

        if ($qrCode === null) {
            $qrCode = UserQRCode::create([
                'user_id' => $userId,
                'identifier' => $userId . time() . mt_rand(100, 1000),
                'expires_at' => Carbon::now()->addSeconds(60),
            ]);
        }

        return $qrCode;
    }

    public function getImageAttribute(): string
    {
        $image = base64_encode(QRCodeProvider::format('png')->backgroundColor(100, 100, 100, 0)->color(250, 250,
            250)->size(250)->generate($this->identifier));

        return $image;
    }

    public function getScannedAtHumanAttribute(): string
    {
        if (isset($this->attributes) && isset($this->attributes['scanned_at'])) {
            return Carbon::parse($this->attributes['scanned_at'])->format('Y-m-d H:i:s l');
        }

        return '';
    }
}
