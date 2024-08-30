<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutCategoryRequest;
use App\Models\WorkoutCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class WorkoutCategoryController extends Controller
{
    /**
     * Fetch library of all categories.
     *
     * @param None
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function library()
    {
        $library = WorkoutCategory::latest()->paginate(10);

        return response()->json($library);
    }

    public function single(WorkoutCategory $category)
    {
        return response()->json($category);
    }

    /**
     * Update category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WorkoutCategory $category, Request $request)
    {
        $category->name = $request->category['name'];
        $category->description = $request->category['description'];

        $category->save();

        return response()->json($category);
    }

    /**
     * Fetch all categories without pagination
     *
     * @param None
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $categories = WorkoutCategory::get();

        return response()->json($categories);
    }

    public function store(WorkoutCategoryRequest $request)
    {
        $category = WorkoutCategory::create([
            'name' => $request->name,
            'slug' => Helper::generateSlug(WorkoutCategory::class, $request->name),
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json($category);
    }

    public function destroy(WorkoutCategory $category)
    {
        if (!strpos($category->image_path, 'placeholder')) {
            Storage::disk('s3')->delete($category->image_path);
        }
        return response()->json($category->delete());
    }


    public function uploadImage(WorkoutCategory $category, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('workout-categories/images', $file, 'public');

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
