<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index(): JsonResponse
    {
        $topics = HelpArticle::get();
        return response()->json($topics);
    }

    public function featured(): JsonResponse
    {
        $topics = HelpArticle::isFeatured()->with('category')->get();
        return response()->json($topics);
    }

    public function single(HelpArticle $article): JsonResponse
    {
        $similar = HelpArticle::where('category_id', $article->category_id)->where('id', '!=', $article->id)->get();

        $article->category = $article->category;

        return response()->json([
            'article' => $article,
            'similar' => $similar,
        ]);
    }

    public function categories()
    {
        $categories = HelpCategory::orderBy('name', 'ASC')->with('articles')->get();
        return response()->json($categories);
    }

    public function singleCategory(HelpCategory $category)
    {
        $category->articles = $category->articles;
        return response()->json($category);
    }

    public function search(Request $request)
    {
        $keyword = $request->search_term;

        $articles = HelpArticle::where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        })
            ->with('category')
            ->get();
        return response()->json($articles);
    }
}
