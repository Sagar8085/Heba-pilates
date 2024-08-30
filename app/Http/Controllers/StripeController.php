<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StripeWebhook;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Models\User;
use App\Services\Stripe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Stripe\StripeClient;

class StripeController extends Controller
{
    private array $tiers = [
        '2for49_tier_one_promo' => 'sub',
        '2for49_tier_two' => 'sub',
        '2for69_tier_one' => 'sub',
        'annual_vip_tier_one' => 'sub',
        'annual_vip_tier_two' => 'sub',
        'once_weekly_tier_one' => 'sub',
        'once_weekly_tier_two' => 'sub',
        'single_session_tier_one' => 'sub',
        'single_session_tier_two' => 'sub',
        'ten_sessions_tier_one' => 'sub',
        'ten_sessions_tier_two' => 'sub',
        'thirty_sessions_tier_one' => 'sub',
        'thirty_sessions_tier_two' => 'sub',
        'twice_weekly_tier_one' => 'sub',
        'twice_weekly_tier_two' => 'sub',
        'unlimited_tier_one' => 'sub',
        'unlimited_tier_two' => 'sub',
    ];

    public function __construct(private StripeClient $stripe)
    {
    }

    private function getStripeSecret()
    {
        return config('services.stripe.secret');
    }

    public function paymentConfirmation(Request $request)
    {
        $request->validate([
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'phone_number' => [
                'required',
            ],
            'gender' => [
                'required',
            ],
            'home_studio_id' => [
                'required',
            ],
            'marketing_hear_about_us' => [
                'required',
            ],
        ], [
            'required' => 'This field is required',
            'unique' => 'Are you sure you don\'t already have an account?',
        ]);

        /** @var User $user */
        $user = User::firstOrCreate([
            'role_id' => 4,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'gender' => $request->input('gender'),
        ]);

        if ($user->memberProfile === null) {
            $user->memberProfile()->create([
                'home_studio_id' => $request->input('home_studio_id'),
            ]);
        }

        if ($user->memberProfile->home_studio_id === null) {
            $user->memberProfile->home_studio_id = $request->input('home_studio_id');
        }

        auth()->login($user);
        $user->makeVisible('api_token');

        return response()->json([
            'user' => $user,
        ]);
    }

    public function incomingWebhook(Request $request, Stripe $stripe)
    {
        StripeWebhook::create([
            'type' => $request['type'],
            'response' => json_encode($request->all()),
        ]);

        /**
         * New subscription has been created.
         *
         */
        if ($request['type'] === 'customer.subscription.created') {

            $sub = Subscription::select('subscriptions.*', 'orders.orderable_id', 'orders.orderable_type',
                'orders.invoice_id')
                ->join('orders', 'orders.orderable_id', 'subscriptions.id')
                ->where('orders.orderable_type', 'App\Models\Subscription')
                ->where('orders.invoice_id', $request['data']['object']['latest_invoice'])
                ->where('subscriptions.stripe_id', $request['data']['object']['id'])
                ->first();

            if ($sub === null) {
                /** Load the stripe customer object. */
                $customerId = $request['data']['object']['customer'];
                $customer = $stripe->loadCustomerById($customerId);

                /** Load the price and product purchased. */
                $priceId = $request['data']['object']['plan']['id'];
                $tier = SubscriptionTier::where('stripe_price_id', $priceId)->first();

                if ($tier != null) {
                    /** Load the user from the customers email address. */
                    $user = User::where('email', $customer->email)->first();

                    /** If we can't find a user, we'll create them. */
                    if ($user === null) {
                        $name = explode(' ', $customer->name);
                        $user = User::create([
                            'email' => $customer->email,
                            'first_name' => isset($name[0]) ? $name[0] : '',
                            'last_name' => isset($name[1]) ? $name[1] : '',
                        ]);
                    }

                    $stripeClient = new StripeClient(['api_key' => config('services.stripe.secret')]);
                    $invoice = $stripeClient->invoices->retrieve($request['data']['object']['latest_invoice'], []);

                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'tier' => $tier->slug,
                        'billing_type' => 'automatic',
                        'renew' => 1,
                        'expires' => Carbon::now()->addMonths(1)->format('Y-m-d') . ' 23:59:59',
                        'online_credits' => $tier->online_credits,
                        'studio_credits' => $tier->studio_credits,
                        'stripe_id' => $request['data']['object']['id'],
                        'payment_intent' => $invoice->payment_intent,
                    ]);

                    $order = Order::create([
                        'member_id' => $user->id,
                        'value' => $invoice->amount_due,
                        'method' => 'stripe',
                        'orderable_id' => $subscription->id,
                        'orderable_type' => 'App\Models\Subscription',
                        'invoice_id' => $request['data']['object']['latest_invoice'],
                    ]);
                }
            }
        }

        if ($request['type'] === 'customer.subscription.updated') {
            $sub = Subscription::select('subscriptions.*', 'orders.orderable_id', 'orders.orderable_type',
                'orders.invoice_id')
                ->join('orders', 'orders.orderable_id', 'subscriptions.id')
                ->where('orders.orderable_type', 'App\Models\Subscription')
                ->where('orders.invoice_id', $request['data']['object']['latest_invoice'])
                ->where('subscriptions.stripe_id', $request['data']['object']['id'])
                ->first();

            if ($sub === null) {
                $latest = Subscription::where('stripe_id', $request['data']['object']['id'])->latest()->first();
                $tier = SubscriptionTier::where('stripe_price_id', $request['data']['object']['plan']['id'])->first();

                $stripeClient = new StripeClient(['api_key' => config('services.stripe.secret')]);
                $invoice = $stripeClient->invoices->retrieve($request['data']['object']['latest_invoice'], []);

                $new = Subscription::create([
                    'user_id' => $latest->user_id,
                    'tier' => $latest->tier,
                    'billing_type' => 'automatic',
                    'starts_at' => Carbon::parse($request['data']['object']['current_period_start'])->format('Y-m-d') . ' 00:00:00',
                    'expires' => Carbon::parse($request['data']['object']['current_period_end'])->format('Y-m-d') . ' 23:59:59',
                    'renew' => 1,
                    'online_credits' => $tier->online_credits,
                    'studio_credits' => $tier->studio_credits,
                    'stripe_id' => $request['data']['object']['id'],
                    'payment_intent' => $invoice->payment_intent,
                ]);

                $order = Order::create([
                    'member_id' => $new->user_id,
                    'value' => $invoice->amount_due,
                    'method' => 'stripe',
                    'orderable_id' => $new->id,
                    'orderable_type' => 'App\Models\Subscription',
                    'stripe_order_id' => '',
                    'invoice_id' => $request['data']['object']['latest_invoice'],
                ]);

                $latest->update([
                    'expires' => Carbon::now()->subDays(1)->format('Y-m-d') . ' 23:59:59',
                    'renew' => 0,
                ]);
            }
        }

        return response()->json(200);
    }

    public function paymentIntent(Request $request)
    {
        StripeWebhook::create([
            'type' => 'payment-intent',
            'response' => json_encode($request->all()),
        ]);

        /**
         * Load the subscription that has this payment intent.
         */
        $sub = Subscription::where('stripe_payment_intent', $request['data']['object']['id'])->first();

        if ($sub !== null) {
            if ($request['type'] === 'payment_intent.payment_failed' || $request['type'] === 'payment_intent.requires_action') {
                $sub->order->delete();
                $sub->delete();
            }
        }

        return response()->json(200);
    }
}
