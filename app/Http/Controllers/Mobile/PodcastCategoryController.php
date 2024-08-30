<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PodcastCategory;
use Illuminate\Http\JsonResponse;

class PodcastCategoryController extends Controller
{

    public function all()
    {
        $categories = PodcastCategory::latest()->get();

        return response()->json($categories);
    }

    public function single(PodcastCategory $category): JsonResponse
    {
        return response()->json($category->load(["podcasts"]));
    }
}
