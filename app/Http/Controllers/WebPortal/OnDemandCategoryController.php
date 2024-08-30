<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\OnDemandCategory;
use Illuminate\Http\Request;

class OnDemandCategoryController extends Controller
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
        $library = OnDemandCategory::select('on_demand_categories.*')->orderBy('on_demand_categories.order',
            'ASC')->with('videos');

        if (auth()->user()->memberProfile->home_studio_id !== null) {
            $library = $library->filterMemberAccess();
        }

        if (auth()->user()->role_id === 4) {
            $library = $library->where('on_demand_categories.id', '!=', 14);
            /** Heba requested this category not be visible to members, but still visible to other users such as admins. */
        }

        $library = $library->get();

        return response()->json($library);
    }

    public function single(Request $request, OnDemandCategory $category)
    {
        // Need to do this for them to output?
        $category->videos = $category->videos;
        return response()->json($category);
    }
}
