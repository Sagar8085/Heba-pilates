<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\WorkoutCategory;
use Illuminate\Http\Request;

class WorkoutCategoryController extends Controller
{
    /**
     * Fetch library of all videos.
     *
     * @param None
     *
     * @return Json
     */
    public function library()
    {
        $library = WorkoutCategory::latest()->paginate(20);

        return response()->json($library);
    }

    public function single(Request $request, WorkoutCategory $category)
    {
        return response()->json($category->with('workouts')->first());
    }
}
