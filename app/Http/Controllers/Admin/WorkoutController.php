<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutEditRequest;
use App\Http\Requests\WorkoutRequest;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkoutController extends Controller
{
    /**
     *   Returns a list of workouts for the admin panel with pagination ordered by latet created
     **/
    public function library()
    {
        $library = Workout::onlyAdminWorkouts()->latest('workouts.created_at')->paginate(20);

        return response()->json($library);
    }

    /**
     * Returns a single workout
     **/
    public function single(Workout $workout)
    {
        $workout->warmup = $workout->warmup;
        $workout->training = $workout->training;
        $workout->cooldown = $workout->cooldown;
        $workout->categories = $workout->categories;

        return $workout;
    }

    /**
     * Stores a workout in the databse
     **/
    public function store(WorkoutRequest $request)
    {
        $workout = Workout::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);

        $workout->categories()->sync(array_column($request->selected_categories, 'id'));

        $exercises = [];

        foreach ($request->selected_warm_ups as $exercise) {
            $exercises[] = ['workout_section_id' => 1, 'exercise_id' => $exercise['id'], 'workout_id' => $workout->id];
        }

        foreach ($request->selected_training as $exercise) {
            $exercises[] = ['workout_section_id' => 2, 'exercise_id' => $exercise['id'], 'workout_id' => $workout->id];
        }

        foreach ($request->selected_cool_downs as $exercise) {
            $exercises[] = ['workout_section_id' => 3, 'exercise_id' => $exercise['id'], 'workout_id' => $workout->id];
        }

        $workout->exercises()->sync($exercises);

        return response()->json($workout);
    }

    /**
     * Soft Delete the workout from the DB
     **/
    public function destroy(Workout $workout)
    {
        // Deletes image from s3 buket if not a placeholder image
        if (!strpos($workout->image_path, 'placeholder')) {
            Storage::disk('s3')->delete($workout->image_path);
        }
        return response()->json($workout->delete());
    }

    /**
     * updates the workout
     **/
    public function update(Workout $workout, Request $request)
    {

        $workout->name = $request->workout['name'];
        $workout->price = $request->workout['price'];
        $workout->description = $request->workout['description'];

        $workout->save();


        $exercises = [];

        foreach ($request->warmupExercises as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 1, true);
        }

        foreach ($request->trainingExercises as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 2, true);
        }

        foreach ($request->cooldownExercises as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 3, true);
        }


        $workout->exercises()->detach();
        $workout->exercises()->sync($exercises);

        return response()->json($workout);
    }

    /**
     * Customises the exercises within a workout with different durations and sets /reps
     **/
    public function customise(Workout $workout, Request $request)
    {
        $exercises = [];

        foreach ($request->workout['warmup'] as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 1, true);
        }

        foreach ($request->workout['training'] as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 2, true);
        }

        foreach ($request->workout['cooldown'] as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 3, true);
        }

        $workout->exercises()->sync($exercises);

        return response()->json($workout);
    }

    /**
     * Uploads an image for the workout
     **/
    public function uploadImage(Workout $workout, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('workout/images', $file, 'public');

                /**
                 * Save the root image path against the resource.
                 */
                $workout->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($workout);
    }

    public function exercises(Request $request, Workout $workout)
    {
        // Calculate the duration of each exercise where not the default;

        $warmup = $workout->warmup->load('warmupSection');
        foreach ($warmup as $exercise) {
            $exercise->section = $exercise->warmupSection[0];
        }
        $training = $workout->training->load('trainingSection');
        foreach ($training as $exercise) {
            $exercise->section = $exercise->trainingSection[0];
        }
        $cooldown = $workout->cooldown->load('cooldownSection');
        foreach ($cooldown as $exercise) {
            $exercise->section = $exercise->cooldownSection[0];
        }

        return response()->json($workout);
    }

    public function editAudience(Workout $workout, Request $request)
    {
        if ($request->user_audience) {
            // UPDATE USERS WITH WORKOUT ACCESS
            $workout->users()->sync(array_column($request->selected_members, 'id'));

            return response()->json([
                "success" => true,
                "message" => 'Successfully updated audience',
                "data" => $request->selected_members,
            ], 200);
        } else {
            // UPDATE WORKOUT CATEGORIES
            $workout->categories()->sync(array_column($request->selected_categories, 'id'));

            return response()->json([
                "success" => true,
                "message" => 'Successfully updated audience',
                "data" => $request->selected_categories,
            ], 200);
        }
    }
}
