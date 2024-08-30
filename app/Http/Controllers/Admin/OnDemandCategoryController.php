<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OnDemandCategoryRequest;
use App\Models\OnDemandCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnDemandCategoryController extends Controller
{

    /**
     * Fetch all on demand video categories.
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return response()->json(
            OnDemandCategory::query()
                ->select('on_demand_categories.*')
                ->filterAdminAccess()
                // HP-264 requests that categories be ordered newest to oldest
                // instead of using the order column.
                ->orderBy('created_at', 'DESC')
                ->paginate(25),
        );
    }

    public function single(OnDemandCategory $category)
    {
        $category->gyms;
        return response()->json($category);
    }

    /**
     * Store a new on demand resource.
     *
     * @param OnDemandCategoryRequest $request
     *
     * @return JsonResponse
     */
    public function store(OnDemandCategoryRequest $request): JsonResponse
    {
        /** @var User|null $user */
        $user = auth()->user();

        $category = OnDemandCategory::create([
            'name' => $request->name,
            'slug' => Helper::generateSlug(OnDemandCategory::class, $request->name),
            'description' => $request->description,
            'created_by' => $user?->id,
        ]);

        // Assign this category to the user's available gyms by default.
        if ($user instanceof User) {
            $category->gyms()->sync($user->accessibleGymsArray());
        }

        return response()->json($category);
    }

    /**
     * Return all Categories assigned to this category and order them.
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderList()
    {
        $categories = OnDemandCategory::orderBy('order', 'ASC')->get();

        return response()->json($categories);
    }

    /**
     * Update category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OnDemandCategory $category, Request $request)
    {
        $category->name = $request->category['name'];
        $category->description = $request->category['description'];
        $category->save();

        $category->gyms()->sync(array_column($request->category['gyms'], 'id'));

        return response()->json($category);
    }

    /**
     * Update category Order.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(OnDemandCategory $category, Request $request)
    {
        foreach ($request->categories as $index => $ondemand) {
            OnDemandCategory::where('id', $ondemand['id'])->update([
                'order' => ($index + 1),
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Process image to store in S3.
     *
     * @param \App\Models\OnDemandCategory $category
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(OnDemandCategory $category, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('on-demand-categories/images', $file, 'public');

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
