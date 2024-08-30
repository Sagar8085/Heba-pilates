<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PodcastRequest;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PodcastController extends Controller
{
    /**
     * Fetch paginated list of all podcasts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $podcasts = Podcast::latest()->paginate(50);

        return response()->json($podcasts);
    }

    /**
     * Store a new podcast resource.
     *
     * @param \App\Http\Requests\PodcastRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PodcastRequest $request)
    {
        $podcast = Podcast::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->user()->id,
            'price' => $request->price,
        ]);

        $podcast->categories()->sync(array_column($request->selected_categories, 'id'));

        return response()->json($podcast);
    }

    /**
     * Load a single podcast resource.
     *
     * @param \App\Models\Podcast $podcast
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Podcast $podcast)
    {
        $podcast->categories = $podcast->categories;

        return response()->json($podcast);
    }

    /**
     * Process image to store in S3.
     *
     * @param \App\Models\Podcast $podcast
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Podcast $podcast, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('podcasts/images', $file, 'public');

                /**
                 * Save the root image path against the resource.
                 */
                $podcast->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($podcast);
    }

    /**
     * Upload recording and store in S3.
     *
     * @param \App\Models\Podcast $podcast
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Podcast $podcast, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the raw video to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('podcasts/audio-recordings', $file, 'public');

                $podcast->update([
                    'audio_path' => $file_path,
                ]);
            }
        }

        return response()->json($podcast);
    }

    /**
     * Update main podcast resource.
     *
     * @param \App\Models\Podcast $podcast
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Podcast $podcast, Request $request)
    {
        $podcast->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        $podcast->categories()->sync(array_column($request->categories, 'id'));

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Update podcast status.
     *
     * @param \App\Models\Podcast $podcast
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Podcast $podcast, Request $request)
    {
        $podcast->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
