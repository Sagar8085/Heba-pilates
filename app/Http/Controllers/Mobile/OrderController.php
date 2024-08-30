<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function userOrders(): JsonResponse
    {
        $member = Member::where('user_id', '=', auth()->user()->id)->first();
        return response()->json($member->orders->load('orderable'));
    }
}
