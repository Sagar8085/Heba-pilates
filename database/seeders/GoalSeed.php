<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Goal;
use Carbon\Carbon;

class GoalSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $goals = [
            ['id' => 1, 'name' => 'Weight Loss', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 2, 'name' => 'Muscle Gain', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 3, 'name' => 'Tone & Shape', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 4, 'name' => 'Muscular Definition', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 5, 'name' => 'Endurance', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 6, 'name' => 'Health Improvement', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 7, 'name' => 'Stress Release', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 8, 'name' => 'Decrease Blood Pressure', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 9, 'name' => 'Improve Posture', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            ['id' => 10, 'name' => 'Improve Flexability', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
        ];

        Goal::insert($goals);
    }
}
