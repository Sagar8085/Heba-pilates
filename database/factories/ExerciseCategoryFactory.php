<?php

namespace Database\Factories;

use App\Models\ExerciseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();

        $image_paths = ['workout-placeholder-images/008.png', 'workout-placeholder-images/007.png', 'workout-placeholder-images/006.png', 'workout-placeholder-images/005.png', 'workout-placeholder-images/004.png', 'workout-placeholder-images/003.png', 'workout-placeholder-images/002.png', 'workout-placeholder-images/001.png'];

        return [
            'name' => $this->faker->company(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'created_by' => $user->id,
            'image_path' => $image_paths[mt_rand(0, 7)],
        ];
    }
}
