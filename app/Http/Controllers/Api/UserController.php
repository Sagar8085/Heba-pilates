<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderCreatedExternally;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserStoreRequest;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Order;
use App\Models\User;

class UserController extends Controller
{
    public function __invoke(UserStoreRequest $request)
    {
        $user = User::create($request->validated());

        $creditPack = CreditPack::query()
            ->where('name', 'Promo 4 sessions')
            ->firstOrFail();

        $creditPackPurchase = CreditPackPurchase::create([
            'user_id' => $user->id,
            'credit_pack_id' => $creditPack->id,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => now()->addDays($creditPack->days_till_expiry),
            'order_id' => 9999,
        ]);

        $order = Order::create([
            'member_id' => $user->id,
            'value' => 0,
            'method' => 'stripe',
            'orderable_id' => $creditPackPurchase->getKey(),
            'orderable_type' => CreditPackPurchase::class,
            'stripe_order_id' => 'blank',
        ]);

        $creditPackPurchase->update([
            'order_id' => $order->getKey(),
        ]);

        OrderCreatedExternally::dispatch($order);

        return response()->json([
            $user->name . ' created successfully',
        ]);
    }
}
