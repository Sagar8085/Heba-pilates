<?php

namespace Database\Factories;

use App\Models\PARQ;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PARQFactory extends Factory
{
    protected $model = PARQ::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'current_injuries' => 0,
            'taking_medication' => 0,
            'advised_by_doctor' => 0,
            'currently_pregnant' => 0,
            'contact_first_name' => $this->faker->firstName,
            'contact_last_name' => $this->faker->lastName,
            'contact_phone_number' => $this->faker->phoneNumber,
            'contact_email' => $this->faker->safeEmail,
            'created_by' => User::factory(),
        ];
    }
}
