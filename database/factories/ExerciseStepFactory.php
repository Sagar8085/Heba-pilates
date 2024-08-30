<?php

namespace Database\Factories;

use App\Models\ExerciseStep;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseStepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseStep::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->paragraph()
        ];
    }
}
