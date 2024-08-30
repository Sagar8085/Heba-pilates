<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Workout;
use App\Models\WorkoutCategory;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Workout::factory()->times(25)->create();

        $categories = WorkoutCategory::get();

        foreach ($categories as $category) {
            $workouts = Workout::inRandomOrder()->take(5)->get();

            $category->workouts()->sync(array_column($workouts->toArray(), 'id'));
        }
    }
}
