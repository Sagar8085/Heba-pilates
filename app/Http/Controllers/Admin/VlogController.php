<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\VlogRequest;
use App\Models\Vlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VlogController extends Controller
{
    /**
     * Fetch library of all vlog posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function library()
    {
        $posts = Vlog::latest()->paginate(25);

        return response()->json($posts);
    }

    /**
     * Store a new vlog resource.
     *
     * @param \App\Http\Requests\VlogRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VlogRequest $request)
    {
        $post = Vlog::create([
            'title' => $request->title,
            'slug' => Helper::generateSlug(Vlog::class, $request->title),
            'excerpt' => substr($request->content, 0, 75),
            'content' => $request->content,
            'video_url' => $request->video_url,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json($post);
    }

    /**
     * Load a single vlog resource.
     *
     * @param \App\Models\Vlog $vlog
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Vlog $vlog)
    {
        return response()->json($vlog);
    }

    /**
     * Update a single vlog resource.
     *
     * @param \App\Models\Vlog $vlog
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Vlog $vlog, Request $request)
    {
        $vlog->update([
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $request->video_url,
        ]);

        return response()->json($vlog);
    }

    /**
     * Process image to store in S3.
     *
     * @param \App\Models\Vlog $vlog
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Vlog $vlog, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('vlogs/images', $file, 'public');

                /**
                 * Save the root image path against the category.
                 */
                $vlog->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($vlog);
    }
}
