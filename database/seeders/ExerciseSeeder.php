<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Exercise;
use App\Models\ExerciseSection;
use App\Models\ExerciseCategory;
use App\Models\ExerciseStep;
use App\Models\Workout;
use App\Models\User;
use App\Models\Focus;
use App\Models\IntensityMET;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Exercise::factory()->times(25)->create();

        $exercises = Exercise::get();
        $workouts = Workout::get();
        $users = User::get();

        foreach ($exercises as $exercise){
            $focuses = Focus::inRandomOrder()->take(mt_rand(1,6))->get()->toArray();
            $exercise->focus()->sync(array_column($focuses, 'id'));

            $sections = ExerciseSection::take(mt_rand(1,3))->get()->toArray();
            $exercise->sections()->sync(array_column($sections, 'id'));

            $intensity = IntensityMET::inRandomOrder()->first();
            $exercise->intensity()->sync($intensity->id);

            $categories = ExerciseCategory::inRandomOrder()->take(10)->get()->toArray();
            $exercise->categories()->sync(array_column($categories, 'id'));

            $stepCount = mt_rand(1,7);
            ExerciseStep::factory()->times($stepCount)->create();

            $steps = ExerciseStep::latest()->inRandomOrder()->take($stepCount)->get()->toArray();
            $exercise->steps()->sync(array_column($steps, 'id'));

        }

        foreach ($workouts as $workout) {
            $exercises = Exercise::inRandomOrder()->take(5)->get();

            $syncExercises = [];
            foreach ($exercises as $exercise ) {
                $storeWorkoutType = $customReps = $customSets = $customDuration = $customRest = null;

                $storeCustom = mt_rand(1,3);
                if ($storeCustom == 1) {
                    $storeWorkoutType = 'duration';
                    $customDuration = mt_rand(30, 180);
                    $customRest = mt_rand(0,30);
                } elseif ($storeCustom == 2) {
                    $storeWorkoutType = 'setsReps';
                    $customSets = mt_rand(1, 3);
                    $customReps = mt_rand(4, 10);
                    $customRest = mt_rand(0,30);
                }
                $syncExercises[] = ['exercise_id' => $exercise->id, 'workout_section_id' => mt_rand(1,3),
                    'store_workout_type' => $storeWorkoutType, 'custom_duration' => $customDuration,
                    'custom_sets' => $customSets, 'custom_reps' => $customReps, 'custom_rest' =>$customRest
                ];
            }

            $workout->exercises()->sync($syncExercises);
        }


        foreach ($users as $user) {
            $customWorkoutCount = mt_rand(1,5);

            Workout::factory()->times($customWorkoutCount)->create();

            $customWorkouts = Workout::latest()->inRandomOrder()->take($customWorkoutCount)->get();

            foreach ($customWorkouts as $workout) {
                $exercises = Exercise::inRandomOrder()->take(5)->get();

                $syncExercises = [];
                foreach ($exercises as $exercise ) {
                    $storeWorkoutType = $customReps = $customSets = $customDuration = $customRest = null;

                    $storeCustom = mt_rand(1,3);
                    if ($storeCustom == 1) {
                        $storeWorkoutType = 'duration';
                        $customDuration = mt_rand(30, 180);
                        $customRest = mt_rand(0,30);
                    } elseif ($storeCustom == 2) {
                        $storeWorkoutType = 'setsReps';
                        $customSets = mt_rand(1, 3);
                        $customReps = mt_rand(4, 10);
                        $customRest = mt_rand(0,30);
                    }
                    $syncExercises[] = ['exercise_id' => $exercise->id, 'workout_section_id' => mt_rand(1,3),
                        'store_workout_type' => $storeWorkoutType, 'custom_duration' => $customDuration,
                        'custom_sets' => $customSets, 'custom_reps' => $customReps, 'custom_rest' =>$customRest
                    ];
                }

                $workout->exercises()->sync($syncExercises);
            }

            $user->workouts()->sync(array_column($customWorkouts->toArray(), 'id'));
        }


    }
}
