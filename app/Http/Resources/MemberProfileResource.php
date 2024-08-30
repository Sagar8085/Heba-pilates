<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MemberProfileResource
 *
 * @package App\Http\Resources
 */
class MemberProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'fitness_goal' => $this->fitness_goal,
            'height' => $this->height,
            'weight' => $this->weight,
            'bmr' => $this->bmr,
            'daily_calory_goal' => $this->daily_calory_goal,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'fitness_level' => $this->fitness_level ?: 0,
            'pilates_experience' => $this->pilates_experience ?: 0,
            'onboarding_complete' => $this->onboarding_complete,
            'tracking_enabled' => $this->tracking_enabled,
            'contract_path' => $this->contract_path,
            'home_studio_id' => $this->home_studio_id,
            'preferred_studio_id' => $this->preferred_studio_id,
            'height_imperial_ft' => $this->height_imperial_ft,
            'height_imperial_inches' => $this->height_imperial_inches,
            'weight_imperial' => $this->weight_imperial,
        ];
    }
}
