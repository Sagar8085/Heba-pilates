<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\WorkoutCategory;


class WorkoutCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WorkoutCategory::factory()->times(8)->create();
    }
}
