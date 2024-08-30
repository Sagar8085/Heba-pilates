<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\PodcastCategory;
use Illuminate\Http\JsonResponse;

class PodcastCategoryController extends Controller
{

    public function all(): JsonResponse
    {
        $categories = PodcastCategory::latest()->get();

        return response()->json($categories);
    }

    public function single($slug): JsonResponse
    {
        $category = PodcastCategory::where('slug', $slug)->with('podcasts')->first();

        return response()->json($category);
    }
}
