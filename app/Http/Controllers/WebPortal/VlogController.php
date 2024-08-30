<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\Vlog;
use Illuminate\Http\JsonResponse;

class VlogController extends Controller
{

    public function all(): JsonResponse
    {
        $posts = Vlog::latest()->get();

        return response()->json($posts);
    }

    public function single($slug): JsonResponse
    {
        $post = Vlog::where('slug', $slug)->first();

        return response()->json($post);
    }
}
