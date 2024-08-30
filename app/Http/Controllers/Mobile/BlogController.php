<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function all(): JsonResponse
    {
        $blogs = Blog::latest()->get();

        return response()->json($blogs);
    }
}
