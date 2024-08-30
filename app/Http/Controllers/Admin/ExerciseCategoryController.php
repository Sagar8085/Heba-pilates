<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseCategoryRequest;
use App\Models\ExerciseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ExerciseCategoryController extends Controller
{
    public function all()
    {
        $categories = ExerciseCategory::latest()->paginate(10);

        return response()->json($categories);
    }

    public function single(ExerciseCategory $category)
    {
        return response()->json($category);
    }

    public function store(ExerciseCategoryRequest $request)
    {
        $category = ExerciseCategory::create([
            'name' => $request->name,
            'slug' => Helper::generateSlug(ExerciseCategory::class, $request->name),
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json($category);
    }

    /**
     * Update category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExerciseCategory $category, Request $request)
    {
        $category->name = $request->category['name'];
        $category->description = $request->category['description'];

        $category->save();

        return response()->json($category);
    }

    /**
     * Soft Delete the workout from the DB
     **/
    public function destroy(ExerciseCategory $category)
    {
        if (!strpos($category->image_path, 'placeholder')) {
            Storage::disk('s3')->delete($category->image_path);
        }
        return response()->json($category->delete());
    }

    public function uploadImage(ExerciseCategory $category, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('exercise-categories/images', $file, 'public');

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
