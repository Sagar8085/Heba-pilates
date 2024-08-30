<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Trait HandlesUsers
 *
 * @paclage Tests\Traits
 */
trait HandlesUsers
{
    protected function mapUsersToPayload(Collection $users)
    {
        return $users->map(function (User $user) {
            return collect([
                'id' => $user->id,
                'role_id' => $user->role_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'gender' => $user->gender,
                'avatar_path' => $user->avatar_path,
                'date_of_birth' => $user->date_of_birth->format('d/m/Y'),
                'age' => $user->age,
                'street_address' => $user->street_address,
                'city' => $user->city,
                'postcode' => $user->postcode,
                'has_rated_app' => !!$user->has_rated_app,
                'email_verified_at' => $user->email_verified_at->format('d/m/Y'),
                'created_at' => $user->created_at->format('d/m/Y'),
                'updated_at' => $user->updated_at->format('d/m/Y'),
                'deleted_at' => $user->deleted_at?->format('d/m/Y'),
                'deleted_by' => $user->deleted_by,
                'is_sales_agent' => !!$user->is_sales_agent,
                'guest_status' => $user->guest_status,
                'subscription_status' => $user->subscription_status,
                'name' => $user->name,
                'name_email' => $user->name_email,
                'avatar' => $user->avatar,
                'calls_made' => $user->calls_made,
                'appointments_made' => $user->appointments_made,
                'signups_made' => $user->signups_made,
                'superadmin' => $user->superadmin,
                'dob_human' => $user->dob_human,
                'created_at_human' => $user->created_at_human,
                'subscription_status_human' => $user->subscription_status_human,
                'subscription' => $user->subscription?->toArray(),
                'recent_reformer_booking' => $user->recent_reformer_booking,
                'most_recent_past_reformer_booking' => $user->mostRecentPastReformerBooking,
                'latest_studio_visit' => $user->latest_studio_visit,
                'lead' => $user->lead,
                'member_profile' => $user->memberProfile ? [
                    'id' => $user->memberProfile->id,
                    'user_id' => $user->memberProfile->user_id,
                    'fitness_goal' => $user->memberProfile->fitness_goal,
                    'height' => $user->memberProfile->height,
                    'weight' => $user->memberProfile->weight,
                    'bmr' => $user->memberProfile->bmr,
                    'daily_calory_goal' => $user->memberProfile->daily_calory_goal,
                    'created_at' => $user->memberProfile->created_at->format('d/m/Y'),
                    'updated_at' => $user->memberProfile->updated_at->format('d/m/Y'),
                    'fitness_level' => $user->memberProfile->fitness_level,
                    'pilates_experience' => $user->memberProfile->pilates_experience,
                    'onboarding_complete' => $user->memberProfile->onboarding_complete,
                    'tracking_enabled' => $user->memberProfile->tracking_enabled,
                    'contract_path' => $user->memberProfile->contract_path,
                    'home_studio_id' => $user->memberProfile->home_studio_id,
                    'preferred_studio_id' => $user->memberProfile->preferred_studio_id,
                    'height_imperial_ft' => $user->memberProfile->height_imperial_ft,
                    'height_imperial_inches' => (float)$user->memberProfile->height_imperial_inches,
                    'weight_imperial' => (float)$user->memberProfile->weight_imperial,
                ] : null,
            ])->pipe(function (Collection $collection) use ($user) {
                return $user->gym ? $collection->put('gym', $user->gym) : $collection;
            })->toArray();
        })->toArray();
    }
}