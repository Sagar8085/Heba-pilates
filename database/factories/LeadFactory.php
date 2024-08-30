<?php

namespace Database\Factories;

use App\Constants\LeadStatuses;
use App\Models\Gym;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->dateTimeBetween(),
            'gender' => $this->faker->randomElement(['male', 'female', 'prefer_not_to_disclose']),
            'status' => $this->faker->randomElement(LeadStatuses::ALL),
            'temperature' => $this->faker->randomElement(['cold', 'warm', 'hot']),
            'assigned_to' => User::factory(),
            'assigned_at' => $this->faker->dateTimeBetween(),
            'source' => $this->faker->randomElement(['walk-in', 'other']),
            'gym_locations' => $this->faker->sentence,
            'heard_from' => $this->faker->sentence,
            'fitness_history' => $this->faker->sentence,
            'previous_gyms' => $this->faker->sentence,
            'last_exercise' => $this->faker->sentence,
            'upcoming_events' => $this->faker->sentence,
            'family_situation' => $this->faker->sentence,
            'sleep_pattern' => $this->faker->sentence,
            'fitness_activities' => $this->faker->sentence,
            'fitness_goal' => null,
            'gym_id' => Gym::factory(),
            'interested' => $this->faker->randomElement([1, 0]),
            'subscribe_weekly' => $this->faker->randomElement([1, 0]),
            'subscribe_monthly' => $this->faker->randomElement([1, 0]),
            'created_at' => ($date = $this->faker->dateTimeBetween()),
            'updated_at' => $date,
        ];
    }
}
