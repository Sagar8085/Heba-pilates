<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadAchievement
 *
 * @property int $id
 * @property int $agent_id
 * @property int $achievement_id
 * @property int $read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement whereAchievementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadAchievement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadAchievement extends Model
{
    use HasFactory;

    protected $table = 'leads_achievements';
}
