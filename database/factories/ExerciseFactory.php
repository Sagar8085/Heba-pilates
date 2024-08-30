<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exercise::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::onlyAdmins()->inRandomOrder()->first();

        $image_paths = ['exercise/images/HvQqwArnSiFXsjuCBC4rZoWMiVXWTbzNBYEVC18I.png', 'exercise/images/q3YaO7uuFYgZDFg7fnVpn3eYxrb5oUZeKwE6mNf7.png', 'exercise/images/0YgvCYBITKNZissqbmt2fZiooQnvTEci6AiqAIk8.png', 'exercise/images/anT87OO0rcK2AxaaQQBHHPRjv8g5SH0IauZIFWZJ.png', 'exercise/images/kfRgSILTPf8Km4KFTxI5R0FTkXGmIU2PbsmAog4n.png', 'exercise/images/guxC0FJFSjTFWovg4fuvK36ccAi3ap6RIlekwLey.png'];
        $paid = 0;

        if (mt_rand(1,100) < 15) {
            $paid = 1;
        }

        return [
            'name'=> $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'created_by' => $user->id,
            'image_path' => $image_paths[mt_rand(0, 5)],
            'video_path' => 'streaming/output/37.m3u8',
            'duration' => mt_rand(30, 500),
            'duration_per_rep' => mt_rand(1, 5),
            'processed' => 1,
            'paid' => $paid
        ];
    }
}
