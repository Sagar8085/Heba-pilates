<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PodcastCategory;

class PodcastCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PodcastCategory::factory()->times(10)->create();
    }
}
