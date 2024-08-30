<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveClassCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveClassCategoryController extends Controller
{
    public function index()
    {
        $categories = LiveClassCategory::all();

        return response()->json($categories);
    }

    /**
     * Store a new category resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $category = LiveClassCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($category);
    }

    /**
     * Load a single category resource.
     *
     * @param App\Models\LiveClassCategory $category
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(LiveClassCategory $category)
    {
        return response()->json($category);
    }

    /**
     * Update a single category resource.
     *
     * @param App\Models\LiveClassCategory $category
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LiveClassCategory $category, Request $request)
    {
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($category);
    }

    /**
     * Process image to store in S3.
     *
     * @param App\Models\LiveClassCategory $category
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(LiveClassCategory $category, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('live-class-categories/images', $file, 'public');

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
