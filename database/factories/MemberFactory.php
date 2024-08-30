<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MemberFactory
 *
 * @package Database\Factories
 */
class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'home_studio_id' => Gym::factory(),
            'preferred_studio_id' => null,
            'contract_path' => $this->faker->word(16, true),
            'fitness_level' => $this->faker->sentence,
            'pilates_experience' => $this->faker->sentence,
            'fitness_goal' => $this->faker->sentence,
            'height' => $this->faker->randomNumber(3),
            'weight' => $this->faker->randomNumber(2),
            'bmr' => $this->faker->randomNumber(2),
            'daily_calory_goal' => $this->faker->randomNumber(4),
            'created_at' => $this->faker->dateTimeBetween(),
            'updated_at' => $this->faker->dateTimeBetween(),
            'onboarding_complete' => $this->faker->boolean(),
            'tracking_enabled' => $this->faker->boolean(),
        ];
    }
}
