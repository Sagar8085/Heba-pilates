<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\Order;
use App\Services\CheckoutSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class CreditPackController extends Controller
{
    public function index()
    {
        $introPurchase = CreditPackPurchase::where('user_id', auth()->user()->id)->where('credit_pack_id', 1)->first();

        if (auth()->user()->subscription) {
            $purchasablePacks = [14, 12, 13];
        } else {
            $purchasablePacks = [2, 12, 13];
        }

        if ($introPurchase === null) {
            $creditPacks = CreditPack::orderBy('price', 'ASC')->whereIn('id', $purchasablePacks)->orWhere('id',
                1)->get();
        } else {
            $creditPacks = CreditPack::orderBy('price', 'ASC')->whereIn('id', $purchasablePacks)->get();
        }

        return response()->json($creditPacks);
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

    public function generateCheckout(CreditPack $creditPack, Request $request)
    {
        $reference = $request->input('reference', '');
        $gymId = $request->input('gymId');

        $stripePriceId = $creditPack
            ->credit_pack_prices()
            ->whereHas('gyms', function ($query) use ($gymId) {
                $query->where('gyms.id', $gymId);
            })
            ->first()?->stripe_price_id ?: $creditPack->stripe_price_id;

        $customer = $this->fetchStripeCustomer();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $stripePriceId,
                    'quantity' => 1,
                ],
            ],
            'mode' => $request->input('mode', CheckoutSession::MODE_PAYMENT),
            'customer' => $customer->id,
            'allow_promotion_codes' => true,
            'success_url' => env('APP_URL') . '/credit-packs/' . $creditPack->id . '/purchase/success?session_id={CHECKOUT_SESSION_ID}&reference=' . $reference,
            'cancel_url' => env('APP_URL') . '/credit-packs/' . $creditPack->id . '/purchase/cancel',
        ]);

        return response()->json($session);
    }

    public function storePurchase(CreditPack $creditPack, Request $request)
    {
        $stripeId = $request->stripeId;

        $stripe = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($stripeId, []);

        $intent = $stripe->paymentIntents->retrieve($session->payment_intent, []);

        $paymentMethod = $intent->payment_method;

        $purchase = CreditPackPurchase::create([
            'user_id' => auth()->user()->id,
            'credit_pack_id' => $creditPack->id,
            'order_id' => 1,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => $creditPack->days_till_expiry !== null ? Carbon::now()->addDays($creditPack->days_till_expiry) : ($creditPack->months_till_expiry !== null ? Carbon::now()->addMonths($creditPack->months_till_expiry) : 0),
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Web',
            'user_id' => auth()->user()->id,
            'object_id' => $purchase->id,
            'object_type' => CreditPackPurchase::class,
            'created_by' => auth()->user()->id,
        ]);

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => $intent->amount_received,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => CreditPackPurchase::class,
            'stripe_order_id' => $intent->id,
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Web',
            'user_id' => auth()->user()->id,
            'object_id' => $order->id,
            'object_type' => 'App\Models\Order',
            'created_by' => auth()->user()->id,
        ]);

        $purchase->sendPurchaseConfirmationEmail();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function myPurchases()
    {
        $purchases = CreditPackPurchase::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->where('studio_credits', '>', 0)
            ->where(function ($query) {
                $query->where('expires', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('expires');
            })
            ->with('pack')
            ->get();

        return response()->json($purchases);
    }
}
