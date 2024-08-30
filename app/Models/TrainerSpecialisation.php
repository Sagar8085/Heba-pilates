<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrainerSpecialisation
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $trainers
 * @property-read int|null $trainers_count
 * @method static \Database\Factories\TrainerSpecialisationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrainerSpecialisation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TrainerSpecialisation extends Model
{
    use HasFactory;

    public function trainers()
    {
        return $this->belongsToMany('App\Models\User', '_pivot_trainer_specialisations', 'trainer_specialisation_id',
            'user_id');
    }
}
