<?php

namespace App\Http\Controllers\WebPortal;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    /**
     * Used to retriece all of the exercises in a workout
     **/
    public function exercises(Request $request, Workout $workout)
    {
        // Calculate the duration of each exercise where not the default;
        $workout->warmup = Helper::calculateExerciseTime($workout->warmup);
        $workout->training = Helper::calculateExerciseTime($workout->training);
        $workout->cooldown = Helper::calculateExerciseTime($workout->cooldown);

        return response()->json($workout);
    }

    /**
     * Adds / renmoves the workout to the users favourites
     **/
    public function favourite(Request $request)
    {
        if ($request->add_to_fav) {
            auth()->user()->workoutFav()->syncWithoutDetaching([$request->workout_id]);
        } else {
            auth()->user()->workoutFav()->detach([$request->workout_id]);
        }

        return response()->json(['message' => 'Successfully synced favourites.']);
    }

    public function stats(Request $request)
    {
        $stats = [
            'kcals_burnt' => $request->kcals_burnt,
            'duration_secs' => $request->duration_secs,
            'workout_id' => $request->workout_id,
            'num_exercises' => $request->num_exercises,
        ];

        auth()->user()->workoutStats()->attach([$stats]);
    }

    public function purchase(Workout $workout): JsonResponse
    {
        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => 299,
            'method' => 'stripe',
            'orderable_id' => $workout->id,
            'orderable_type' => 'App\Models\Workout',
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
