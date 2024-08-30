<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{

    public function all(): JsonResponse
    {
        $posts = Blog::latest()->get();

        return response()->json($posts);
    }

    public function single($slug): JsonResponse
    {
        $post = Blog::where('slug', $slug)->with('author')->first();

        return response()->json($post);
    }
}
