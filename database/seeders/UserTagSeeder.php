<?php

namespace Database\Seeders;

use App\Models\UserTag;
use Illuminate\Database\Seeder;

/**
 * Class UserTagSeeder
 *
 * @package Database\Seeders
 */
class UserTagSeeder extends Seeder
{
    public function run()
    {
        collect([
            [
                'name' => 'Medical Condition - Asthma',
                'slug' => 'medical-condition-asthma',
            ],
            [
                'name' => 'Medical Condition - Bad Heart',
                'slug' => 'medical-condition-bad-heart',
            ],
            [
                'name' => 'Heart',
                'slug' => 'heart',
            ],
            [
                'name' => 'Injury',
                'slug' => 'injury',
            ],
            [
                'name' => 'Corporate',
                'slug' => 'corporate',
            ],
        ])->each(fn (array $data) => UserTag::create($data));
    }
}
