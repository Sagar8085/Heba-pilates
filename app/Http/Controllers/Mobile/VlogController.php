<?php

namespace App\Http\Controllers\Mobile;

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

    public function single(Vlog $vlog): JsonResponse
    {
        return response()->json($vlog);
    }
}
