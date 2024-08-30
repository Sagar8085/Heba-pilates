<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OnDemand;
use App\Models\OnDemandCategory;

class OnDemandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OnDemand::factory()->times(100)->create();

        /**
         * Add each video to some categories.
         */
        $classes = OnDemand::get();

        foreach ($classes as $class) {

            // Fetch some random categories.
            $number = mt_rand(1, 4);
            $categories = OnDemandCategory::inRandomOrder()->take($number)->get()->toArray();

            $class->categories()->sync(array_column($categories, 'id'));

        }
    }
}
