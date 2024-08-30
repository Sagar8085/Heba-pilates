<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PARQ
 *
 * @property int $id
 * @property int $user_id
 * @property int $current_injuries
 * @property string|null $current_injuries_details
 * @property int $taking_medication
 * @property string|null $taking_medication_details
 * @property int $advised_by_doctor
 * @property string|null $advised_by_doctor_details
 * @property int $currently_pregnant
 * @property string|null $currently_pregnant_details
 * @property string $contact_first_name
 * @property string $contact_last_name
 * @property string $contact_phone_number
 * @property string $contact_email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \App\Models\User|null $creator
 * @property-read string $created_at_human
 * @method static \Database\Factories\PARQFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ query()
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereAdvisedByDoctor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereAdvisedByDoctorDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereContactFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereContactLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereContactPhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereCurrentInjuries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereCurrentInjuriesDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereCurrentlyPregnant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereCurrentlyPregnantDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereTakingMedication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereTakingMedicationDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PARQ whereUserId($value)
 * @mixin \Eloquent
 */
class PARQ extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parq_responses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'current_injuries',
        'current_injuries_details',
        'taking_medication',
        'taking_medication_details',
        'advised_by_doctor',
        'advised_by_doctor_details',
        'currently_pregnant',
        'currently_pregnant_details',
        'contact_first_name',
        'contact_last_name',
        'contact_phone_number',
        'contact_email',
        'created_by',
    ];

    protected $appends = [
        'created_at_human',
    ];

    /**
     * Get the human-readable created at attribute.
     * @return string
     */
    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at->format('g:ia l dS F Y');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
