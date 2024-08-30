<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadActivityType
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadActivityType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadActivityType extends Model
{
    use HasFactory;

    protected $table = 'leads_activity_types';
}
