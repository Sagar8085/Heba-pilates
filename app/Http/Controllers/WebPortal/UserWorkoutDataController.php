<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\UserWorkoutData;
use Illuminate\Http\Request;

class UserWorkoutDataController extends Controller
{
    /**
     * Store the completion data of a workout for a user
     **/
    public function store(Request $request)
    {
        $userWorkoutData = UserWorkoutData::create([
            'user_id' => auth()->user()->id,
            'workout_id' => $request->workout_id,
            'duration' => $request->duration,
            'kcals_burnt' => $request->kcals_burnt,
            'num_exercises' => $request->num_exercises,
        ]);

        return response()->json($userWorkoutData);
    }
}
