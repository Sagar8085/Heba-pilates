<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseVideoRequest;
use App\Jobs\ProcessVideoJob;
use App\Models\Exercise;
use App\Models\ExerciseSection;
use App\Models\ExerciseStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    /**
     * Fetch library of Exercises
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function library()
    {
        $library = Exercise::latest()->with(['categories', 'focus', 'sections'])->paginate(8);

        return response()->json($library);
    }

    /**
     * Fetch single Exercise.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Exercise $exercise)
    {
        $exercise->focus = $exercise->focus;
        $exercise->categories = $exercise->categories;
        $exercise->sections = $exercise->sections;

        return response()->json($exercise);
    }

    /**
     * Store Exercise.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExerciseVideoRequest $request)
    {
        $exercise = Exercise::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'duration_per_rep' => $request->duration_per_rep,
            'created_by' => auth()->user()->id,
            'paid' => $request->paid ? 1 : 0,
        ]);

        foreach ($request->steps as $step) {
            $steps[] = ExerciseStep::create([
                'text' => $step['text'],
            ]);
        }

        $exercise->categories()->sync(array_column($request->selected_categories, 'id'));
        $exercise->sections()->sync(array_column($request->selected_sections, 'id'));
        $exercise->focus()->sync(array_column($request->selected_focuses, 'id'));
        $exercise->intensity()->sync($request->selected_intensity['id']);
        $exercise->steps()->sync(array_column($steps, 'id'));

        return response()->json($exercise);
    }

    /**
     * Update Exercise.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Exercise $exercise, Request $request)
    {
        $exercise->name = $request->exercise['name'];
        $exercise->description = $request->exercise['description'];
        $exercise->duration = $request->exercise['duration'];
        $exercise->duration_per_rep = $request->exercise['duration_per_rep'];
        $exercise->paid = $request->exercise['paid'] ? 1 : 0;

        $exercise->save();

        $exercise->categories()->sync(array_column($request->exercise['categories'], 'id'));
        $exercise->sections()->sync(array_column($request->exercise['sections'], 'id'));
        $exercise->focus()->sync(array_column($request->exercise['focus'], 'id'));
        $exercise->intensity()->sync($request->exercise['intensity']['id']);

        $steps = [];
        foreach ($request->exercise['steps'] as $step) {
            $steps[] = ExerciseStep::create([
                'text' => $step['text'],
            ]);
        }

        $exercise->steps()->sync(array_column($steps, 'id'));
    }

    /**
     * Soft Delete the exercise from the DB
     **/
    public function destroy(Exercise $exercise)
    {

        if (!strpos($exercise->image_path, 'placeholder')) {
            Storage::disk('s3')->delete($exercise->image_path);
        }

        return response()->json($exercise->delete());
    }

    public function uploadImage(Exercise $exercise, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('exercise/images', $file, 'public');

                /**
                 * Save the root image path against the resource.
                 */
                $exercise->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($exercise);
    }

    public function uploadVideo(Exercise $exercise, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the raw video to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3videos')->putFile('exercise/raw-videos', $file, 'public');

                $exercise->update([
                    'video_path' => $file_path,
                ]);
                /**
                 * Queues the AWS Elastic Transcoder Job which will process the video into a streaming format.
                 * We will set this queue to it's own specific 'transcoding' pipeline so it doesn't conflict with any other queue workers.
                 */
                ProcessVideoJob::dispatch($exercise)->onQueue('transcoding');
            }
        }

        return response()->json($exercise);
    }

    // Sections are fixed and not to be changed
    public function sections()
    {
        $exercises = ExerciseSection::all();
        $sections = [];

        $sections['warmups'] = $exercises[0]->warmups;
        $sections['training'] = $exercises[1]->training;
        $sections['cooldowns'] = $exercises[2]->cooldowns;


        return response()->json($sections);
    }

    public function exercisesWithSection($focus)
    {
        $sections = [];


        $attributes = [
            'created_human',
            'excerpt',
            'video',
            'steps',
        ];


        $sections['warmups'] = Exercise::where('paid', 0)
            ->whereHas('focus', function ($query) use ($focus) {
                $query->where('focus.id', $focus);
            })->whereHas('sections', function ($query) {
                $query->where('exercise_sections.id', 1);
            })->with('sections', function ($query) {
                $query->where('exercise_sections.id', 1);
            })->get();
        foreach ($sections['warmups'] as $exercise) {
            $exercise->section = $exercise->sections[0];
            $exercise->makeHidden($attributes);
            $exercise->pivot = (object)[
                "store_workout_type" => null,
                "custom_reps" => null,
                "custom_rest" => null,
                "custom_sets" => null,
                "custom_duration" => null,
            ];
        }

        $sections['training'] = Exercise::where('paid', 0)
            ->whereHas('focus', function ($query) use ($focus) {
                $query->where('focus.id', $focus);
            })->whereHas('sections', function ($query) {
                $query->where('exercise_sections.id', 2);
            })->with('sections', function ($query) {
                $query->where('exercise_sections.id', 2);
            })->get();
        foreach ($sections['training'] as $exercise) {
            $exercise->section = $exercise->sections[0];
            $exercise->makeHidden($attributes);
            $exercise->pivot = (object)[
                "store_workout_type" => null,
                "custom_reps" => null,
                "custom_rest" => null,
                "custom_sets" => null,
                "custom_duration" => null,
            ];
        }

        $sections['cooldown'] = Exercise::where('paid', 0)
            ->whereHas('focus', function ($query) use ($focus) {
                $query->where('focus.id', $focus);
            })->whereHas('sections', function ($query) {
                $query->where('exercise_sections.id', 3);
            })->with('sections', function ($query) {
                $query->where('exercise_sections.id', 3);
            })->get();
        foreach ($sections['cooldown'] as $exercise) {
            $exercise->section = $exercise->sections[0];
            $exercise->makeHidden($attributes);
            $exercise->pivot = (object)[
                "store_workout_type" => null,
                "custom_reps" => null,
                "custom_rest" => null,
                "custom_sets" => null,
                "custom_duration" => null,
            ];
        }

        return response()->json($sections);
    }
}
