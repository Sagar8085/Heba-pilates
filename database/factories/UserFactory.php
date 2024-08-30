<?php

namespace Database\Factories;

use App\Constants\Genders;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role_id' => 4,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'phone_number' => $this->faker->phoneNumber,
            'gender' => $this->faker->randomElement(Genders::ALL),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'date_of_birth' => $this->faker->dateTimeThisCentury(),
            'street_address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'guest_status' => $this->faker->randomElement([
                'Idle Guest',
                'Lapsed Guest',
                'Prospect',
                'Active Guest',
            ]),
            'postcode' => $this->faker->postcode,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
