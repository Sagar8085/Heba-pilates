<?php

namespace Database\Factories;

use App\Models\TrainerSpecialisation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerSpecialisationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainerSpecialisation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'name' => $this->faker->name
        ];
    }
}
