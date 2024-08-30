<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Workout;
use App\Models\WorkoutCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function main(): JsonResponse
    {
        $categories = WorkoutCategory::orderBy('name', 'ASC')->get();
        $myWorkouts = auth()->user()->workouts;

        return response()->json([
            'categories' => $categories,
            'my_workouts' => $myWorkouts,
        ]);
    }

    /**
     * Return a workout category along with the associated workouts
     **/
    public function category(WorkoutCategory $category)
    {
        return response()->json($category->load('workouts'));
    }

    /**
     * Return a single workout and it's exercises
     **/
    public function single(Workout $workout)
    {
        return response()->json($workout->load('exercises'));
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

    // Save stats of a workout
    public function stats(Request $request)
    {
        $stats = [
            'kcals_burnt' => $request->kcals_burnt,
            'duration_secs' => $request->duration_secs,
            'workout_id' => $request->workout_id,
            'num_exercises' => $request->num_exercises,
        ];

        auth()->user()->workoutStats()->attach([$stats]);

        return response()->json(['message' => 'Successfully saved Stats.']);

    }

    public function purchase(Request $request): JsonResponse
    {
        $this->validate($request, [
            'workout_id' => 'required',
            'payment_method' => 'required',
            'price' => 'required',
        ]);

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => $request->price,
            'method' => $request->payment_method,
            'orderable_id' => $request->workout_id,
            'orderable_type' => 'App\Models\Workout',
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
