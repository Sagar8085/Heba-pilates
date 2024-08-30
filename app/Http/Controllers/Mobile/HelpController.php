<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = HelpCategory::with('articles')->get();
        return response()->json($categories);
    }

    public function featured(): JsonResponse
    {
        $topics = HelpArticle::isFeatured()->with('category')->get();
        return response()->json($topics);
    }

    public function search(Request $request): JsonResponse
    {
        $articles = HelpArticle::where('name', 'like', '%' . $request->search_term . '%')->with('category')->get();
        return response()->json($articles);
    }
}
