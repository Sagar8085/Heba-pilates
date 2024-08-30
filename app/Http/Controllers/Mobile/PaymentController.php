<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\Order;
use App\Models\SubscriptionTier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use stdClass;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    private function getStripeSecret()
    {
        return config('services.stripe.secret');
    }

    public function capturePaymentFromToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'amount' => 'required',
            'credit_pack_id' => 'required',
        ]);

        $introPackPurchase = CreditPackPurchase::whereIn('credit_pack_id', [1])->where('user_id',
            auth()->user()->id)->first();

        if ($introPackPurchase !== null && $request->credit_pack_id === 1) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You have already purchased an Intro Pack, a maximum of 1 per account is permitted.',
            ]);
        }

        $creditPack = CreditPack::find($request->credit_pack_id);

        $stripeSecret = config('services.stripe.secret');

        try {
            $stripe = new StripeClient($stripeSecret);

            $stripe->charges->create([
                'amount' => $request->amount,
                'currency' => 'gbp',
                'source' => $request->token,
                'description' => 'Purchase of Credit Pack - ' . $creditPack->name . ' by ' . auth()->user()->name . ' #' . auth()->user()->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failure',
                'message' => $e->getMessage(),
            ]);
        }

        $purchase = CreditPackPurchase::create([
            'user_id' => auth()->user()->id,
            'credit_pack_id' => $creditPack->id,
            'order_id' => 1,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => $creditPack->days_till_expiry !== null ? Carbon::now()->addDays($creditPack->days_till_expiry) : ($creditPack->months_till_expiry !== null ? Carbon::now()->addMonths($creditPack->months_till_expiry) : 0),
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via App',
            'user_id' => auth()->user()->id,
            'object_id' => $purchase->id,
            'object_type' => 'App\Models\CreditPackPurchase',
            'created_by' => auth()->user()->id,
        ]);

        $purchase->sendPurchaseConfirmationEmail();

        Order::create([
            'member_id' => auth()->user()->id,
            'value' => $request->amount ? $request->amount : $creditPack->price,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => 'App\Models\CreditPackPurchase',
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via App',
            'user_id' => auth()->user()->id,
            'object_id' => $order->id,
            'object_type' => 'App\Models\Order',
            'created_by' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment has been successful',
        ]);
    }

    private function reduceNumberByPercentage($percent, $original)
    {
        //The percent that we want to add on to our number.
        $percentageChange = (0 - $percent);

        //Our original number.
        $originalNumber = $original;

        //Get 2.25% of 100.
        $numberToAdd = ($originalNumber / 100) * $percentageChange;

        //Finish it up with some simple addition
        $newNumber = $originalNumber + $numberToAdd;

        //Result is 102.25
        return $newNumber;
    }

    public function createPaymentIntent(Request $request)
    {
        $promoResponse = null;
        $promoCodeMessage = null;

        $stripeSecret = config('services.stripe.secret');

        $creditPack = CreditPack::find($request->credit_pack_id);
        $price = $creditPack->price;

        if ($creditPack === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find credit pack with ID ' . $request->credit_pack_id,
            ]);
        }

        $stripe = new StripeClient($stripeSecret);

        if ($request->promo_code != '') {
            $request = $stripe->promotionCodes->all(['code' => $request->promo_code]);

            if (isset($request['data'][0])) {
                $promoCode = $request['data'][0]['coupon'];

                $coupon = $stripe->coupons->retrieve($request['data'][0]['coupon']->id, ['expand' => ['applies_to']]);

                if (!isset($coupon->applies_to) || in_array($creditPack->stripe_product_id,
                        $coupon->applies_to->products)) {
                    if ($promoCode->amount_off !== null) {
                        $price = ($creditPack->price - $promoCode->amount_off);
                    }

                    if ($promoCode->percent_off !== null) {
                        $price = $this->reduceNumberByPercentage($promoCode->percent_off, $creditPack->price);
                    }

                    $promoResponse = new stdClass;
                    $promoResponse->name = $promoCode->name;
                    $promoResponse->new_price = '£' . ($price / 100);

                    $promoCodeMessage = 'Your promo code has been successfully applied.';
                } else {
                    $promoCodeMessage = 'This promo code cannot be applied to this product.';
                }
            } else {

                $promoCodeMessage = 'This is not a valid promo code, or cannot be applied to this product.';

            }
        }

        $intent = $stripe->paymentIntents->create([
            'amount' => $price,
            'currency' => 'gbp',
            'payment_method_types' => ['card'],
        ]);

        return response()->json([
            'status' => 'success',
            'payment_intent' => $intent->id,
            'payment_intent_secret' => $intent->client_secret,
            'customer' => null,
            'ephemeral_key' => null,
            'promo_code' => $promoResponse,
            'promo_code_message' => $promoCodeMessage
            // 'intent' => $intent
        ]);
    }

    public function createMembershipPaymentIntent(Request $request)
    {
        $stripeSecret = config('services.stripe.secret');

        $subscription = SubscriptionTier::find($request->membership_id);

        if ($subscription === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find membership with ID ' . $request->membership_id,
            ]);
        }

        $stripe = new StripeClient($stripeSecret);

        $intent = $stripe->paymentIntents->create([
            'amount' => $subscription->price,
            'currency' => 'gbp',
            'payment_method_types' => ['bacs_debit'],
        ]);

        return response()->json([
            'status' => 'success',
            'payment_intent' => $intent->id,
            'payment_intent_secret' => $intent->client_secret,
            'customer' => null,
            'ephemeral_key' => null,
        ]);
    }

    public function capturePaymentFromCard(Request $request)
    {

    }

    public function completePaymentIntent($paymentIntent, Request $request)
    {
        $request->validate([
            'credit_pack_id' => 'required',
        ]);

        $creditPack = CreditPack::find($request->credit_pack_id);

        $purchase = CreditPackPurchase::create([
            'user_id' => auth()->user()->id,
            'credit_pack_id' => $creditPack->id,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => $creditPack->days_till_expiry !== null ? Carbon::now()->addDays($creditPack->days_till_expiry) : ($creditPack->months_till_expiry !== null ? Carbon::now()->addMonths($creditPack->months_till_expiry) : 0),
        ]);

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => $creditPack->price,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => 'App\Models\CreditPackPurchase',
            'stripe_order_id' => $paymentIntent,
        ]);

        $purchase->sendPurchaseConfirmationEmail();

        return response()->json([
            'status' => 'success',
            'message' => 'Your purchase is complete!',
        ]);
    }
}
