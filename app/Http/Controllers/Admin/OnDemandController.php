<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OnDemandVideoRequest;
use App\Jobs\ProcessVideoJob;
use App\Models\OnDemand;
use App\Models\OnDemandCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnDemandController extends Controller
{
    /**
     * Fetch library of all videos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function library()
    {
        $library = OnDemand::select('on_demand_videos.*')->filterAdminAccess()->with('categories')->paginate(25);

        return response()->json($library);
    }

    /**
     * Store a new on demand resource.
     *
     * @param \App\Http\Requests\OnDemandVideoRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OnDemandVideoRequest $request)
    {
        $ondemand = OnDemand::create([
            'name' => $request->name,
            'duration' => round($request->duration * 100),
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);
        $ondemand->categories()->sync(array_column($request->categories, 'id'));

        return response()->json($ondemand);
    }

    /**
     * Load a single on-demand resource.
     *
     * @param \App\Models\OnDemand $ondemand
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(OnDemand $ondemand)
    {
        $ondemand->categories = $ondemand->categories;

        return response()->json($ondemand);
    }

    /**
     * Update a single on demand resource.
     *
     * @param \App\Models\OnDemand $ondemand
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OnDemand $ondemand, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'duration' => 'required|numeric',
            'published' => 'required',
        ]);

        $ondemand->update([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => round($request->duration * 100),
            'published' => $request->published,
        ]);

        $ondemand->categories()->sync(array_column($request->categories, 'id'));

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Process image to store in S3.
     *
     * @param \App\Models\OnDemand $ondemand
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(OnDemand $ondemand, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk(config('ondemand.image_disk'))->putFile('on-demand/images', $file, 'public');

                /**
                 * Save the root image path against the resource.
                 */
                $ondemand->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($ondemand);
    }

    public function uploadVideo(OnDemand $ondemand, Request $request): JsonResponse
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the raw video to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk(config('ondemand.video_disk'))->putFile('on-demand/raw-videos/hebapilates',
                    $file, 'public');

                $ondemand->update([
                    'video_path' => $file_path,
                ]);

                /**
                 * Queues the AWS Elastic Transcoder Job which will process the video into a streaming format.
                 * We will set this queue to it's own specific 'transcoding' pipeline so it doesn't conflict with any other queue workers.
                 */
                ProcessVideoJob::dispatchIf(config('ondemand.video_transcode'), $ondemand)->onQueue('transcoding');
            }
        }

        return response()->json($ondemand);
    }

    /**
     * Delete an on demand resource.
     *
     * @param App\Models\OnDemand $ondemand
     *
     * @return Json
     */
    public function destroy(OnDemand $ondemand)
    {
        $ondemand->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Return all classes assigned to this category and order them.
     *
     * @param App\Models\OnDemandCategory $category
     *
     * @return Json
     */
    public function orderList(OnDemandCategory $category)
    {
        $classes = OnDemand::select('on_demand_videos.*')
            ->join('_pivot_on_demand_categories', '_pivot_on_demand_categories.on_demand_id', 'on_demand_videos.id')
            ->orderBy('_pivot_on_demand_categories.order')
            ->where('_pivot_on_demand_categories.category_id', $category->id)
            ->get();


        return response()->json($classes);
    }

    /**
     * Update the order of all the on-demand classes with their position in the array.
     * We are increasing the index by 1 because we don't want the order column to start at 0.
     *
     * @param App\Models\OnDemandCategory $category
     * @param Illuminate\Http\Request $request
     *
     * @return Json
     */
    public function updateOrder(OnDemandCategory $category, Request $request, OnDemand $ondemand)
    {
        \DB::table('_pivot_on_demand_categories')->where('category_id', $category->id)->delete();

        $rows = $request->classes;
        foreach ($rows as $index => $row) {
            \DB::table('_pivot_on_demand_categories')->insert([
                'on_demand_id' => $row['id'],
                'category_id' => $category['id'],
                'order' => $index,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }
}
