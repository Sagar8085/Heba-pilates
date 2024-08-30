<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Podcast;
use App\Models\PodcastCategory;

class PodcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Podcast::factory()->times(50)->create();

        /**
         * Add each podcast to a category.
         */
        $podcasts = Podcast::get();

        foreach ($podcasts as $podcast) {
            // Fetch a random category.
            $number = mt_rand(1, 10);
            $categories = PodcastCategory::where('id', $number)->get()->toArray();

            $podcast->categories()->sync(array_column($categories, 'id'));
        }
    }
}
