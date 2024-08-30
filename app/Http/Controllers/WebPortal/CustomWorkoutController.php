<?php

namespace App\Http\Controllers\WebPortal;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomWorkoutRequest;
use App\Models\Workout;
use Illuminate\Support\Facades\Storage;
use stdClass;


class CustomWorkoutController extends Controller
{
    public function store(CustomWorkoutRequest $request)
    {

        $user_id = auth()->user()->id;

        $workout = Workout::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => (float)$request->price,
            'created_by' => $user_id,
            'image_path' => $request->image_path,
        ]);

        $exercises = [];

        foreach ($request->warmupExercises as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 1);
        }

        foreach ($request->trainingExercises as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 2);
        }

        foreach ($request->cooldownExercises as $exercise) {
            $exercises[] = Helper::sortCustomWorkoutDurations($exercise, $workout, 3);
        }

        $workout->exercises()->sync($exercises);
        $workout->users()->syncWithoutDetaching($user_id);

        return response()->json($workout);
    }

    /**
     * retreives all of the placeholder images from the AWS bucket
     **/
    public function placeholders()
    {
        $images = Storage::disk('s3')->allFiles('workout-placeholder-images');
        $returnArray = [];
        $i = 1;
        foreach ($images as $image) {
            $obj = new stdClass();
            $obj->id = $i;
            $obj->src = $image;
            $obj->alt = 'A placeholder image for your custom workout';
            $returnArray[] = $obj;
            $i++;
        }

        return $returnArray;
    }


}
