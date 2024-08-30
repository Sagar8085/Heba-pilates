<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\LiveClassController;
use App\Http\Controllers\WebPortal\CreditPackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Models\Order;
use App\Models\Event;
use Carbon\Carbon;
use Stripe\StripeClient;

Route::get('stripe-callback/app/{user}/{slug}/bacs/{status}', function () {
    $status = request('status');

    if (request('status') === 'success') {
        $tier = SubscriptionTier::where('slug', request('slug'))->first();

        $stripeId = request('session_id');

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve($stripeId, []);

        $intent = $stripe->paymentIntents->retrieve($session->payment_intent, []);

        if ($intent->status === 'succeeded' || $intent->status === 'processing') {
            $paymentMethod = $intent->payment_method;

            // $paymentMethod = 'pm_1IqIu2Klaf0ZXKqlzzwIQlGl';

            $sub = Subscription::create([
                'user_id' => request('user'),
                'tier' => request('slug'),
                'expires' => Carbon::now()->addMonths(1),
                'renew' => 1,
                'studio_credits' => $tier->studio_credits,
                'stripe_id' => $paymentMethod,
                'stripe_payment_intent' => $intent->id,
            ]);

            Event::create([
                'message' => 'Purchased Subscriptions via App',
                'user_id' => request('user'),
                'object_id' => $sub->id,
                'object_type' => 'App\Models\Subscription',
                'created_by' => request('user'),
            ]);

            $sub->sendPurchaseConfirmationEmail();

            $order = Order::create([
                'member_id' => request('user'),
                'value' => $tier->price,
                'method' => 'stripe',
                'orderable_id' => $sub->id,
                'orderable_type' => 'App\Models\Subscription',
            ]);

            Event::create([
                'message' => 'Purchased Subscriptions via App',
                'user_id' => request('user'),
                'object_id' => $order->id,
                'object_type' => 'App\Models\Order',
                'created_by' => request('user'),
            ]);
        } else {
            $status = 'failure';
        }
    }

    return view('stripeAppConfirmation', compact('status'));
});

Route::get('qr', function () {
    return view('qr');
});

Route::get('live/{liveclass}/stream', [LiveClassController::class, 'streamLiveClass']);
Route::get('admin/live-classes/{liveclass}/stream',
    [App\Http\Controllers\Admin\LiveClassController::class, 'publishLiveClassStream']);

Route::get('/admin/{vue_capture?}', function () {
    return view('admin.app');
})->where('vue_capture', '[\/\w\.-]*');

Route::get('/tablet/{vue_capture?}', function () {
    if (env("APP_URL") === "https://sandbox.hebepilates.olivex.co.uk") {
        return redirect('https://app.hebapilates.com/tablet');
    }
    return view('tablet.app');
})->where('vue_capture', '[\/\w\.-]*');

Route::get('/{vue_capture?}', function () {
    return view('webportal.app');
})->where('vue_capture', '[\/\w\.-]*');
