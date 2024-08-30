<?php

namespace Database\Factories;

use App\Models\Gym;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GymFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Gym::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $companyName = $this->faker->company;

        return [
            'name' => $companyName,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'corporate_name' => strtoupper($companyName),
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'street_address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode
        ];
    }
}
