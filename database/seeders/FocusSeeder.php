<?php

namespace Database\Seeders;

use App\Models\Focus;
use Illuminate\Database\Seeder;

/**
 * Class FocusSeeder
 *
 * @package Database\Seeders
 */
class FocusSeeder extends Seeder
{
    public function run()
    {
        collect([
            [
                'name' => 'Body shape and tone',
                'slug' => 'general',
            ],
            [
                'name' => 'Fitness and strength',
                'slug' => 'endurance',
            ],
            [
                'name' => 'Posture and joints',
                'slug' => 'flexibility',
            ],
            [
                'name' => 'Energy and mood',
                'slug' => 'mobility',
            ],
        ])->each(fn (array $data) => Focus::factory()->create($data));
    }





}
