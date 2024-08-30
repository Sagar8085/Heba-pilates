<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{

    public function all(): JsonResponse
    {
        $courses = Course::latest()->get();

        return response()->json($courses);
    }

    public function single($slug): JsonResponse
    {
        $course = Course::where('slug', $slug)->with('episodes')->first();

        return response()->json($course);
    }

    public function purchase($slug): JsonResponse
    {
        $course = Course::where('slug', $slug)->first();

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => 999,
            'method' => 'stripe',
            'orderable_id' => $course->id,
            'orderable_type' => 'App\Models\Course',
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
