<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MarketingPreference
 *
 * @property int $id
 * @property int $member_id
 * @property int $account
 * @property int $new_content
 * @property int $bookings
 * @property int $marketing
 * @property string|null $heard_about_us
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereBookings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereHeardAboutUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereMarketing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereNewContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingPreference whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MarketingPreference extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'account',
        'new_content',
        'bookings',
        'marketing',
        'heard_about_us',
    ];
}
