<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\UserWorkoutData;
use App\Models\Workout;
use App\Models\WorkoutCategory;
use Illuminate\Http\JsonResponse;

class WorkoutPenetrationController extends Controller
{
    public function topWorkouts(WorkoutCategory $category): JsonResponse
    {
        $classIds = $category->workouts->pluck('id')->toArray();

        $classes = collect();

        foreach ($classIds as $id) {
            $class = Workout::find($id);
            $views = UserWorkoutData::where('workout_id', $id)->count();

            $classes->push([
                'id' => $class->id,
                'name' => $class->name,
                'views' => $views,
            ]);
        }

        $sorted = $classes->sortByDesc('views')->values()->all();

        return response()->json($sorted);
    }
}
