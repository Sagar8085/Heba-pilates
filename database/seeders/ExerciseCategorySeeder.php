<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ExerciseCategory;

class ExerciseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ExerciseCategory::factory()->times(10)->create();
    }
}
