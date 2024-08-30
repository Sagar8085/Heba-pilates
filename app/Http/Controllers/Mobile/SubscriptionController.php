<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StripePaymentMethod;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    private function getStripeSecret()
    {
        return config('services.stripe.secret');
    }

    private function fetchStripeCustomer()
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $customer = $stripe->customers->all(['email' => auth()->user()->email]);


        if (count($customer->data) === 0) {
            $customer = $stripe->customers->create([
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ]);
        } else {
            $customer = $customer->data[(count($customer) - 1)];
        }

        return $customer;
    }

    public function index(): JsonResponse
    {
        $memberships = SubscriptionTier::where('admin_only', 0)->get();

        return response()->json($memberships);
    }

    public function purchaseBacsMembership(Request $request): JsonResponse
    {
        $stripeSecret = config('services.stripe.secret');

        $tier = SubscriptionTier::where('id', $request->subscriptionTierId)->first();

        $customer = $this->fetchStripeCustomer();

        Stripe::setApiKey($stripeSecret);

        $params = [
            'payment_method_types' => ['bacs_debit', 'card'],
            'line_items' => [
                [
                    'price' => $tier->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'customer' => $customer->id,
            'allow_promotion_codes' => true,
            'success_url' => env('APP_URL') . '/stripe-callback/app/' . auth()->user()->id . '/' . $tier->slug . '/bacs/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('APP_URL') . '/stripe-callback/app/' . auth()->user()->id . '/' . $tier->slug . '/bacs/cancel',
        ];

        if ($tier->getTierPrice()?->recurring === null) {
            $params['payment_intent_data'] = [
                'setup_future_usage' => 'off_session',
            ];
        }

        $session = Session::create($params);

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ]);

        $request->validate([
            'sortCode' => 'required',
            'accountNumber' => 'required',
            'addressOne' => 'required',
            'city' => 'required',
            'postCode' => 'required',
        ]);

        $sortCode = $request->sortCode;

        $stripe = new StripeClient($stripeSecret);

        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'bacs_debit',
            'billing_details' => [
                'address' => [
                    'line1' => $request->addressOne,
                    'city' => $request->city,
                    'postal_code' => $request->postCode,
                    'country' => 'GB',
                ],
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
            ],
            'bacs_debit' => [
                'account_number' => $request->accountNumber,
                'sort_code' => $sortCode,
            ],
        ]);

        StripePaymentMethod::where('user_id', auth()->user()->id)->where('type',
            'bacs_debit')->update(['default' => 0]);

        StripePaymentMethod::create([
            'user_id' => auth()->user()->id,
            'payment_method' => $paymentMethod->id,
            'default' => 1,
            'type' => 'bacs_debit',
        ]);

        $tier = SubscriptionTier::where('id', $request->subscriptionTierId)->first();

        $stripe->setupIntents->create([
            'confirm' => true,
            'payment_method' => $paymentMethod->id,
            'payment_method_types' => ['bacs_debit'],
            'usage' => 'off_session',
        ]);

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $tier->price,
            'currency' => 'gbp',
            'payment_method' => $paymentMethod->id,
            'payment_method_types' => ['bacs_debit'],
            'off_session' => true,
            'confirm' => true,
        ]);

        try {
            $stripe->paymentIntents->capture(
                $paymentIntent->id,
                []
            );

            $sub = Subscription::create([
                'user_id' => auth()->user()->id,
                'tier' => $tier->slug,
                'expires' => Carbon::now()->addMonth(1),
                'renew' => 1,
            ]);

            Order::create([
                'member_id' => auth()->user()->id,
                'value' => $tier->price,
                'method' => 'stripe',
                'orderable_id' => $sub->id,
                'orderable_type' => 'App\Models\Subscription',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Your Gym Membership has been activated!',
            ]);
        } catch (Exception $e) {
            Log::debug($e);
            return response()->json([
                'status' => 'failure',
                'message' => 'There was a problem deducting funds from your bank, please make sure your details are correct.',
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'payment_method' => 'required',
            'price' => 'required',
        ]);

        $sub = Subscription::create([
            'user_id' => auth()->user()->id,
            'tier' => $request->type,
            'expires' => Carbon::now()->addMonths(1)->format('Y-m-d H:i:s'),
            'renew' => 1,
        ]);

        // TODO: Also create order here
        Order::create([
            'member_id' => auth()->user()->id,
            'value' => $orderValue,
            'method' => $request->payment_method,
            'orderable_id' => $sub->id,
            'orderable_type' => 'App\Models\Subscription',
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function captureSubscription(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'payment_method' => 'required',
        ]);

        $subscriptionTier = SubscriptionTier::where('product_id', $request->product_id)->first();

        if ($subscriptionTier->slug === 'vip-unlimited') {
            $months = 12;
        } else {
            $months = 1;
        }

        $sub = Subscription::create([
            'user_id' => auth()->user()->id,
            'tier' => $subscriptionTier->slug,
            'online_credits' => $subscriptionTier->online_credits,
            'studio_credits' => $subscriptionTier->studio_credits,
            'expires' => Carbon::now()->addMonths($months)->format('Y-m-d H:i:s'),
            'renew' => 1,
        ]);

        // TODO: Also create order here
        Order::create([
            'member_id' => auth()->user()->id,
            'value' => $subscriptionTier->price,
            'method' => $request->payment_method,
            'orderable_id' => $sub->id,
            'orderable_type' => 'App\Models\Subscription',
        ]);

        return response()->json([
            'status' => 'success',
            'sub' => $sub,
        ]);
    }

    public function cancelSubscription(Request $request)
    {
        auth()->user()->subscription->cancel('App');

        return response()->json([
            'status' => 'success',
        ]);
    }
}
