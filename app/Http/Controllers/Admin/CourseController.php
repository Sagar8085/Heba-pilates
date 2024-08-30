<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEpisode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function library(): JsonResponse
    {
        $courses = Course::latest()->with('episodes')->paginate(25);

        return response()->json($courses);
    }

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'benefits' => 'required',
            'description' => 'required',
            'price' => 'required',
            'release_frequency' => 'required',
        ]);

        $course = Course::create([
            'name' => $request->name,
            'slug' => Helper::generateSlug(Course::class, $request->name),
            'price' => $request->price,
            'benefits' => $request->benefits,
            'description' => $request->description,
            'release_frequency' => $request->release_frequency,
        ]);

        return response()->json($course);
    }

    public function single(Course $course): JsonResponse
    {
        return response()->json($course);
    }

    public function update(Course $course, Request $request): JsonResponse
    {
        $course->update([
            'name' => $request->name,
            'benefits' => $request->benefits,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function uploadImage(Course $course, Request $request): JsonResponse
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('courses/images', $file, 'public');

                /**
                 * Save the root image path against the resource.
                 */
                $course->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($course);
    }

    public function uploadVideo(Course $course, Request $request): JsonResponse
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the raw video to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3videos')->putFile('courses/raw-videos', $file, 'public');

                $course->update([
                    'video_path' => $file_path,
                ]);

                /**
                 * Queues the AWS Elastic Transcoder Job which will process the video into a streaming format.
                 * We will set this queue to it's own specific 'transcoding' pipeline so it doesn't conflict with any other queue workers.
                 */
                // ProcessVideoJob::dispatch($ondemand)->onQueue('transcoding');
            }
        }

        return response()->json($course);
    }

    public function storeEpisode(Course $course, Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required',
            'video' => 'required',
        ]);

        $episode = CourseEpisode::create([
            'course_id' => $course->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($episode);
    }

    public function uploadEpisodeImage(Course $course, CourseEpisode $episode, Request $request): JsonResponse
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('episodes/images', $file, 'public');

                /**
                 * Save the root image path against the resource.
                 */
                $episode->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($episode);
    }

    public function uploadEpisodeVideo(Course $course, CourseEpisode $episode, Request $request): JsonResponse
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the raw video to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('episodes/videos', $file, 'public');

                $episode->update([
                    'video_path' => $file_path,
                ]);

                /**
                 * Queues the AWS Elastic Transcoder Job which will process the video into a streaming format.
                 * We will set this queue to it's own specific 'transcoding' pipeline so it doesn't conflict with any other queue workers.
                 */
                // ProcessVideoJob::dispatch($ondemand)->onQueue('transcoding');
            }
        }

        return response()->json($episode);
    }
}
