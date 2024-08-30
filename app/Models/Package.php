<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Package
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property int $credits
 * @property int $session_length
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Carbon $created_human
 * @property-read String $price_human
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereSessionLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Package extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trainer_id',
        'name',
        'description',
        'price',
        'credits',
        'session_length',
    ];

    /**
     * The attributes that are appended to each response.
     *
     * @var array
     */
    protected $appends = [
        'created_human',
        'price_human',
    ];

    public function getCreatedHumanAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('g:ia l dS F Y');
    }

    public function getPriceHumanAttribute(): string
    {
        return '£' . number_format($this->price / 100, 2);
    }
}
