<?php

namespace App\Models;

use App\Collections\OrderCollection;
use Database\Factories\MemberFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Member
 *
 * @property int|null $id
 * @property int|null $user_id
 * @property int|null $home_studio_id
 * @property int|null $preferred_studio_id
 * @property string|null $contract_path
 * @property int|null $fitness_level
 * @property int|null $pilates_experience
 * @property string|null $fitness_goal
 * @property int|null $height
 * @property int|null $weight
 * @property int|null $bmr
 * @property int|null $daily_calory_goal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $onboarding_complete
 * @property int|null $tracking_enabled
 * @property-read mixed $height_imperial_ft
 * @property-read mixed $height_imperial_inches
 * @property-read mixed $weight_imperial
 * @property-read Gym|null $homeStudio
 * @property-read OrderCollection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Gym|null $preferredStudio
 * @property-read Collection|MemberPackage[] $purchasedPackages
 * @property-read int|null $purchased_packages_count
 * @property-read User|null $user
 * @method static MemberFactory factory(...$parameters)
 * @method static Builder|Member newModelQuery()
 * @method static Builder|Member newQuery()
 * @method static Builder|Member query()
 * @method static Builder|Member whereBmr($value)
 * @method static Builder|Member whereContractPath($value)
 * @method static Builder|Member whereCreatedAt($value)
 * @method static Builder|Member whereDailyCaloryGoal($value)
 * @method static Builder|Member whereFitnessGoal($value)
 * @method static Builder|Member whereFitnessLevel($value)
 * @method static Builder|Member whereHeight($value)
 * @method static Builder|Member whereHomeStudioId($value)
 * @method static Builder|Member whereId($value)
 * @method static Builder|Member whereOnboardingComplete($value)
 * @method static Builder|Member wherePilatesExperience($value)
 * @method static Builder|Member wherePreferredStudioId($value)
 * @method static Builder|Member whereTrackingEnabled($value)
 * @method static Builder|Member whereUpdatedAt($value)
 * @method static Builder|Member whereUserId($value)
 * @method static Builder|Member whereWeight($value)
 * @mixin Eloquent
 */
class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'home_studio_id',
        'preferred_studio_id',
        'fitness_level',
        'pilates_experience',
        'fitness_goal',
        'height',
        'weight',
        'bmr',
        'daily_calory_goal',
        'onboarding_complete',
        'tracking_enabled',
        'contract_path',
    ];

    protected $appends = [
        'height_imperial_ft',
        'height_imperial_inches',
        'weight_imperial',
    ];

    /**
     *   Converts the Height attribute from cm to Feet, not including part feet, i.e 5 i.e 180cm = 5
     **/
    public function getHeightImperialFtAttribute()
    {
        return ~~($this->height / 30.48);
    }

    /**
     *   Converts the Height attribute from cm to inches, takes away the feet to 2 dp i.e 180cm = 10.87
     **/
    public function getHeightImperialInchesAttribute()
    {
        return round(($this->height * 0.3937007874) - (~~($this->height / 30.48) * 12), 2);
    }

    /**
     *   Converts the weight from kg to lbs to 2 dp
     **/
    public function getWeightImperialAttribute()
    {
        return round($this->weight * 2.20462, 2);
    }

    /**
     *   Converts the original fitness goal attribute to a user-friendly message
     **/
    public function getFitnessGoalAttribute()
    {
        switch ($this->original['fitness_goal']) {
            case 'same':
                return 'Maintain Weight';
                break;
            case 'drop_fat':
                return 'Lose Weight';
                break;
            case 'gain_muscle':
                return 'Gain Muscle';
                break;
            default:
                return $this->original['fitness_goal'];
        }
    }

    public function purchasedPackages(): HasMany
    {
        return $this->hasMany(MemberPackage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function homeStudio(): HasOne
    {
        return $this->hasOne(Gym::class, 'id', 'home_studio_id');
    }

    public function preferredStudio(): HasOne
    {
        return $this->hasOne(Gym::class, 'id', 'preferred_studio_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
