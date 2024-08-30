<?php

namespace App\Http\Resources;

use App\Models\PARQ;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @property int $id
 * @property int $role_id
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $email
 * @property mixed $phone_number
 * @property mixed $avatar_path
 * @property mixed $gender
 * @property mixed $date_of_birth
 * @property mixed $age
 * @property mixed $street_address
 * @property mixed $city
 * @property mixed $postcode
 * @property mixed $has_rated_app
 * @property mixed $email_verified_at
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $deleted_at
 * @property mixed $deleted_by
 * @property mixed $is_sales_agent
 * @property mixed $guest_status
 * @property mixed $subscription_status
 * @property mixed $name
 * @property mixed $name_email
 * @property mixed $avatar
 * @property mixed $calls_made
 * @property mixed $appointments_made
 * @property mixed $signups_made
 * @property mixed $superadmin
 * @property mixed $dob_human
 * @property mixed $created_at_human
 * @property mixed $subscription_status_human
 * @property mixed $subscription
 * @property mixed $recent_reformer_booking
 * @property mixed $nextReformerBooking
 * @property mixed $latest_studio_visit
 * @property mixed $lead
 * @property mixed $gym
 * @property mixed $available_credits
 * @property mixed $total_studio_visits
 * @property mixed $visits_per_week
 * @property mixed $lastActivityCall
 * @property mixed $lastSentEmail
 * @property mixed $sentEmails
 * @property PARQ $parq
 *
 * @package App\Http\Resources
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'avatar_path' => $this->avatar_path,
            'date_of_birth' => $this->date_of_birth?->format('d/m/Y'),
            'age' => $this->age,
            'street_address' => $this->street_address,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'has_rated_app' => !!$this->has_rated_app,
            'email_verified_at' => $this->email_verified_at?->format('d/m/Y'),
            'created_at' => $this->created_at?->format('d/m/Y'),
            'updated_at' => $this->updated_at?->format('d/m/Y'),
            'deleted_at' => $this->deleted_at?->format('d/m/Y'),
            'deleted_by' => $this->deleted_by,
            'is_sales_agent' => !!$this->is_sales_agent,
            'guest_status' => $this->guest_status,
            'subscription_status' => $this->subscription_status,
            'name' => $this->name,
            'name_email' => $this->name_email,
            'avatar' => $this->avatar,
            'calls_made' => $this->calls_made,
            'emails_sent' => $this->sentEmails->count(),
            'appointments_made' => $this->appointments_made,
            'signups_made' => $this->signups_made,
            'superadmin' => $this->superadmin,
            'dob_human' => $this->dob_human,
            'created_at_human' => $this->created_at_human,
            'subscription_status_human' => $this->subscription_status_human,
            'subscription' => $this->subscription,
            'recent_reformer_booking' => $this->recent_reformer_booking,
            'most_recent_past_reformer_booking' => new ReformerBookingResource($this->whenLoaded('mostRecentPastReformerBooking')),
            'next_reformer_booking' => new ReformerBookingResource($this->nextReformerBooking),
            'latest_studio_visit' => $this->latest_studio_visit,
            'lead' => new LeadResource($this->lead),
            'member_profile' => new MemberProfileResource($this->whenLoaded('memberProfile')),
            'gym' => new GymResource($this->gym),
            'credit_pack_purchase' => new CreditPackPurchaseResource($this->whenLoaded('creditPackPurchase')),
            'available_credits' => $this->available_credits,
            'total_studio_visits' => $this->total_studio_visits,
            'visits_per_week' => $this->visits_per_week,
            'last_call' => new LeadCallResource($this->lastActivityCall),
            'last_sent_email' => new LeadActivityLogResource($this->lastSentEmail),
            'total_spend' => number_format($this->total_spend ?: 0, 2),
            'expected_future_value' => number_format($this->expected_future_value ?: 0, 2),
            'parq' => new ParqResource($this->parq),
        ];
    }
}
