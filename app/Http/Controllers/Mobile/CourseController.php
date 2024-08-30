<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Member;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{

    public function all(): JsonResponse
    {
        $courses = Course::latest()->get();

        return response()->json($courses);
    }

    public function single(Course $course): JsonResponse
    {
        return response()->json($course->load('episodes'));
    }

    public function purchase(Course $course): JsonResponse
    {

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

    public function purchases()
    {
        $member = Member::where('user_id', auth()->user()->id)->first();
        $orders = Order::where('orderable_type', 'App\Models\Course')->where('member_id',
            $member->id)->with('orderable')->get();
        return $orders;
    }
}
