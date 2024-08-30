<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PodcastCategoryRequest;
use App\Models\PodcastCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PodcastCategoryController extends Controller
{

    /**
     * Fetch all on podcast categories.
     *
     * @param None
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $categories = PodcastCategory::latest()->with('podcasts')->get();

        return response()->json($categories);
    }

    /**
     * Store a new podcast resource.
     *
     * @param App\Http\Requests\PodcastCategoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PodcastCategoryRequest $request)
    {
        $category = PodcastCategory::create([
            'name' => $request->name,
            'slug' => Helper::generateSlug(PodcastCategory::class, $request->name),
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json($category);
    }

    /**
     * Load a single podcast category resource.
     *
     * @param App\Models\PodcastCategory $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(PodcastCategory $category)
    {
        return response()->json($category);
    }

    /**
     * Update a single podcast category resource.
     *
     * @param App\Models\PodcastCategory $category
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PodcastCategory $category, Request $request)
    {
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Delete a single podcast category resource.
     *
     * @param App\Models\PodcastCategory $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PodcastCategory $category)
    {
        $category->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Process image to store in S3.
     *
     * @param \App\Models\PodcastCategory $category
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(PodcastCategory $category, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('podcast-categories/images', $file, 'public');

                /**
                 * Save the root image path against the category.
                 */
                $category->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($category);
    }

}
