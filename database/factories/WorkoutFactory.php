<?php

namespace Database\Factories;

use App\Models\Workout;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workout::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::onlyAdmins()->inRandomOrder()->first();

        $image_paths = ['workout-placeholder-images/008.png', 'workout-placeholder-images/007.png', 'workout-placeholder-images/006.png', 'workout-placeholder-images/005.png', 'workout-placeholder-images/004.png', 'workout-placeholder-images/003.png', 'workout-placeholder-images/002.png', 'workout-placeholder-images/001.png'];

        return [
            'name'=> $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'created_by' => $user->id,
            'image_path' => $image_paths[mt_rand(0, 7)]
        ];
    }
}
