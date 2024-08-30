<?php

namespace App\Http\Controllers;

use App\Models\ApiHistory;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BulletDigitalController extends Controller
{
    public function onboardMember(Request $request)
    {
        $apiHistory = new ApiHistory();

        $apiHistory->request = $request->all();
        $apiHistory->headers = \Request::header();

        $token = $request->bearerToken();

        if ($token !== 'dcktfvhkAfRyAbrH7dLXeVB3RM8zKvU7XAUJHCv8wVG98v7uhw9YCXVn6SCHUbCrkHDA9NVzLhWvyS8LdXM2AWsrv4Y7tatLLE7') {
            $json = [
                'status' => 'Unauthorised',
                'message' => 'Your request is either; missing the Authorization header or your Bearer Token is in-correct.',
            ];
            $apiHistory->response = $json;
            $apiHistory->status_code = 401;
            $apiHistory->save();
            return response()->json($json, 401);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $json = ['message' => 'The given data was invalid', 'errors' => $validator->messages()];
            $apiHistory->response = $json;
            $apiHistory->status_code = 422;
            $apiHistory->save();
            return response()->json($json, 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user === null) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);
        }

        $user->sendBulletDigitalWelcomeEmail();

        $creditPack = CreditPack::where('id', 1)->first();

        $purchase = CreditPackPurchase::create([
            'user_id' => $user->id,
            'credit_pack_id' => $creditPack->id,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => $creditPack->days_till_expiry !== null ? Carbon::now()->addDays($creditPack->days_till_expiry) : ($creditPack->months_till_expiry !== null ? Carbon::now()->addMonths($creditPack->months_till_expiry) : 0),
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Bullet Digital API',
            'user_id' => $user->id,
            'object_id' => $purchase->id,
            'object_type' => 'App\Models\CreditPackPurchase',
            'created_by' => $user->id,
        ]);

        $order = Order::create([
            'member_id' => $user->id,
            'value' => 3600,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => 'App\Models\CreditPackPurchase',
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Bullet Digital API',
            'user_id' => $user->id,
            'object_id' => $order->id,
            'object_type' => 'App\Models\Order',
            'created_by' => $user->id,
        ]);

        $json = ['status' => 'success'];
        $apiHistory->response = $json;
        $apiHistory->status_code = 200;
        $apiHistory->save();

        return response()->json($json);
    }

    /**
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlimitedPromo(Request $request)
    {
        $apiHistory = new ApiHistory();

        $apiHistory->request = $request->all();
        $apiHistory->headers = \Request::header();

        $token = $request->bearerToken();

        if ($token !== 'dcktfvhkAfRyAbrH7dLXeVB3RM8zKvU7XAUJHCv8wVG98v7uhw9YCXVn6SCHUbCrkHDA9NVzLhWvyS8LdXM2AWsrv4Y7tatLLE7') {
            $json = [
                'status' => 'Unauthorised',
                'message' => 'Your request is either; missing the Authorization header or your Bearer Token is in-correct.',
            ];
            $apiHistory->response = $json;
            $apiHistory->status_code = 401;
            $apiHistory->save();
            return response()->json($json, 401);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $json = ['message' => 'The given data was invalid', 'errors' => $validator->messages()];
            $apiHistory->response = $json;
            $apiHistory->status_code = 422;
            $apiHistory->save();
            return response()->json($json, 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user === null) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => isset($request->last_name) && trim($request->last_name) != '' ? $request->last_name : ' ',
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);
            $user->sendBulletDigitalWelcomeEmail();
        }

        $creditPack = CreditPack::where('id', 11)->first();

        $purchase = CreditPackPurchase::create([
            'user_id' => $user->id,
            'credit_pack_id' => $creditPack->id,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => $creditPack->days_till_expiry !== null ? Carbon::now()->addDays($creditPack->days_till_expiry) : ($creditPack->months_till_expiry !== null ? Carbon::now()->addMonths($creditPack->months_till_expiry) : 0),
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Bullet Digital API',
            'user_id' => $user->id,
            'object_id' => $purchase->id,
            'object_type' => 'App\Models\CreditPackPurchase',
            'created_by' => $user->id,
        ]);

        $order = Order::create([
            'member_id' => $user->id,
            'value' => 4900,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => 'App\Models\CreditPackPurchase',
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Bullet Digital API',
            'user_id' => $user->id,
            'object_id' => $order->id,
            'object_type' => 'App\Models\Order',
            'created_by' => $user->id,
        ]);

        $json = ['status' => 'success'];
        $apiHistory->response = $json;
        $apiHistory->status_code = 200;
        $apiHistory->save();

        return response()->json($json);
    }
}
