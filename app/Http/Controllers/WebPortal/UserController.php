<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\ContentPreference;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\MarketingPreference;
use App\Models\Member;
use App\Models\OnDemandCategory;
use App\Models\PasswordReset;
use App\Models\QRCode;
use App\Models\ReformerBooking;
use App\Models\StripePaymentMethod;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Models\User;
use App\Models\UserQRCode;
use App\Services\CheckoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class UserController extends Controller
{
    private function fetchStripeCustomer()
    {
        $stripe = new StripeClient('sk_test_51IQzRJKlaf0ZXKql9K197KQneuCbhLreLyCMZqgWBpUp7UyhdCuxs9TEcisIiOSjstOrh67ZE8meQKUrQTD01UX100HA6dUbxs');

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

    /**
     * Send forgot password email
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user !== null) {
            $user->sendResetPasswordEmail();

            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'failure',
        ]);
    }

    /**
     * Reset users password.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        /**
         * Load reset password request.
         */
        $checkToken = PasswordReset::where('token', $request->token)->first();

        if ($checkToken !== null) {
            $user = User::where('id', $checkToken->user_id)->first();

            if ($user !== null) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                return response()->json([
                    'status' => 'success',
                ]);
            }
        }

        return response()->json([
            'status' => 'failure',
        ]);
    }

    /**
     * Update a users name.
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     *
     */
    public function updateName(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        auth()->user()->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updatePhone(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required',
        ]);

        auth()->user()->update([
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function checkPhone(Request $request)
    {
        $user = auth()->user();
        return response()->json($user);
    }

    /**
     * Update a users email address.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function updateEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => auth()->user()->email,
            'password' => $request->password,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            SendEmailJob::dispatch($this, 'emails.user.email-updated', 'Your Email Has Been Updated',
                ['user' => $this])->onQueue('account-notifications');

            auth()->user()->update([
                'email' => $request->email,
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'error',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        $credentials = [
            'email' => auth()->user()->email,
            'password' => $request->current_password,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            auth()->user()->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'error',
        ]);
    }

    public function updateFitness(Request $request)
    {
        $member = Member::where('user_id', auth()->id())->first();
        if (!$member) {
            $member = Member::create([
                'user_id' => auth()->id(),
                'fitness_goal' => $request->focus,
                'height' => $request->height,
                'weight' => $request->weight,
                'bmr' => $request->userDetails['BMR'],
                'daily_calory_goal' => $request->userDetails['targetCals'],
            ]);
        } else {
            $member->fitness_goal = $request->focus;
            $member->height = $request->height;
            $member->weight = $request->weight;
            $member->bmr = $request->userDetails['BMR'];
            $member->daily_calory_goal = $request->userDetails['targetCals'];
            $member->save();
        }

        return response()->json($member);
    }

    /**
     * Used to retriece all of a users custom workouts.
     **/
    public function customWorkout(Request $request)
    {
        // $user = User::find($request->user()->id);
        return response()->json(auth()->user()->workouts);
    }

    public function storeSubscription($tier, Request $request)
    {
        Subscription::create([
            'user_id' => auth()->id(),
            'tier' => $tier,
            'expires' => now()->addMonths(1)->format('Y-m-d H:i:s'),
            'renew' => 1,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Updates the height and weight of the user
     **/
    public function updateHeightWeight(Request $request)
    {
        $member = Member::where('user_id', auth()->id())->first();

        if (!$member) {
            Member::create([
                'user_id' => auth()->id(),
                'height' => $request->height,
                'weight' => $request->weight,
            ]);
        } else {
            $member->height = $request->height;
            $member->weight = $request->weight;

            $member->save();
        }

        return response()->json($member);
    }

    /**
     * Reset a users API token, this will log them out of all devices.
     *
     *
     * @return JsonResponse
     */
    public function resetApiToken()
    {
        $user = auth()->user();

        $user->api_token = $user->generateUniqueApiToken();
        $user->save();

        $user->makeVisible('api_token');

        return response()->json([
            'api_token' => $user->api_token,
        ]);
    }

    /**
     * Load a users notification preferences.
     *
     *
     * @return JsonResponse
     */
    public function notificationPreferences()
    {
        $preferences = MarketingPreference::where('member_id', auth()->id())->first();
        return response()->json($preferences);
    }

    /**
     * Update a users notification preferences.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateNotificationPreferences(Request $request)
    {
        $preferences = MarketingPreference::where('member_id', auth()->id())->first();
        $preferences->update([
            'account' => $request->preferences['account'] ? 1 : 0,
            'new_content' => $request->preferences['new_content'] ? 1 : 0,
            'bookings' => $request->preferences['bookings'] ? 1 : 0,
            'marketing' => $request->preferences['marketing'] ? 1 : 0,
        ]);
        return response()->json($preferences);
    }

    /**
     * Load a users content preferences.
     *
     *
     * @return JsonResponse
     */
    public function contentPreferences()
    {
        $categories = OnDemandCategory::get();
        $contentPreferences = ContentPreference::where('user_id',
            auth()->id())->get()->pluck('category_id')->toArray();

        $cats = collect();

        foreach ($categories as $category) {
            $cats->push([
                'label' => $category->name,
                'value' => $category->id,
            ]);
        }

        return response()->json([
            'categories' => $cats,
            'preferences' => $contentPreferences,
        ]);
    }

    public function updateContentPreferences(Request $request)
    {
        $preference = ContentPreference::where('user_id', auth()->id())->where('category_id',
            $request->preference)->first();

        if ($preference === null) {
            ContentPreference::create([
                'user_id' => auth()->id(),
                'category_id' => $request->preference,
            ]);
        } else {
            $preference->delete();
        }

        return response()->json(200);
    }

    public function createStripeCustomer()
    {
        $stripe = new StripeClient('sk_test_51IQzRJKlaf0ZXKql9K197KQneuCbhLreLyCMZqgWBpUp7UyhdCuxs9TEcisIiOSjstOrh67ZE8meQKUrQTD01UX100HA6dUbxs');
        return $stripe->customers->create([
            'description' => 'My First Test Customer',
        ]);
    }

    public function generateStripeBacsCheckout(Request $request)
    {
        echo 'testing'
            die();
       
          $tier = $request->input('tier');
        $subscriptionTier = SubscriptionTier::find($tier);
          $customer = $this->fetchStripeCustomer();


       Stripe::setApiKey('sk_test_51IQzRJKlaf0ZXKql9K197KQneuCbhLreLyCMZqgWBpUp7UyhdCuxs9TEcisIiOSjstOrh67ZE8meQKUrQTD01UX100HA6dUbxs');
   
        $session = Session::create([
            'payment_method_types' => ['bacs_debit', 'card'],
            'line_items' => [
                [
                    'price' => 'price_1Lh58YKlaf0ZXKqlPj4qq3cN',
                    'quantity' => 1,
                ],
            ],
            //'mode' => $request->input('mode', CheckoutSession::MODE_PAYMENT),
            'mode' => 'subscription',
            'customer' => $customer->id,
            'allow_promotion_codes' => true,
            'success_url' => env('APP_URL'). '/membership/' . $tier . '/bacs/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('APP_URL') . '/membership/' . $tier . '/bacs/cancel',
            
        ]);
        echo "<pre>";
        print_r($session);
      die();

        return response()->json($session);
    }

    public function storeMembership(Request $request, $tier)
    {
        if ($tier === 'vip-unlimited') {
            $months = 12;
        } else {
            $months = 1;
        }

        /**
         * Load the stripe session.
         */
        $stripeId = $request->stripeId;

        $stripe = new StripeClient('sk_test_51IQzRJKlaf0ZXKql9K197KQneuCbhLreLyCMZqgWBpUp7UyhdCuxs9TEcisIiOSjstOrh67ZE8meQKUrQTD01UX100HA6dUbxs');
        $session = $stripe->checkout->sessions->retrieve($stripeId, []);
        $intent = null;

        if ($session->payment_intent) {
            $intent = $stripe->paymentIntents->retrieve($session->payment_intent, []);

            $paymentMethod = $intent->payment_method;

            $method = $stripe->paymentMethods->retrieve($paymentMethod, []);

            StripePaymentMethod::where('user_id', auth()->id())->update(['default' => 0]);

            StripePaymentMethod::create([
                'user_id' => auth()->id(),
                'payment_method' => $paymentMethod,
                'type' => $method->type,
                'default' => 1,
            ]);
        }

        $subscriptionTier = SubscriptionTier::find($tier)->first();

        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'tier' => $tier,
            'expires' => now()->addMonths($months),
            'renew' => 1,
            'online_credits' => $subscriptionTier->online_credits,
            'studio_credits' => $subscriptionTier->studio_credits,
            'stripe_id' => $paymentMethod ?? $session->subscription_id,
            'stripe_payment_intent' => $intent?->id ?: '',
        ]);

        Event::create([
            'message' => 'Purchased Subscription via Web',
            'user_id' => auth()->id(),
            'object_id' => $subscription->id,
            'object_type' => Subscription::class,
            'created_by' => auth()->id(),
        ]);

        $subscription->sendPurchaseConfirmationEmail();

        return response()->json(200);
    }

    public function cancelSubscription()
    {
        $subscription = Subscription::where('user_id', auth()->id())->latest()->first();
        $subscription->cancel();

        return response()->json(200);
    }

    public function myReservations()
    {
        $reservations = ReformerBooking::select('reformer_bookings.*', 'reformers.gym_id')
            ->where('datetime', '>=', now()->format('Y-m-d') . ' 00:00:00')
            ->where('user_id', auth()->id())
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->with('reformer.gym')
            ->orderBy('reformer_bookings.datetime', 'ASC')
            ->get();

        return response()->json($reservations);
    }

    public function uploadCustomAvatar(Request $request)
    {
        $file = $request->file('files')[0];

        $storagePath = Storage::disk('s3')->putFile('user-avatars', $file, 'public');

        auth()->user()->update(['avatar_path' => $storagePath]);

        return response()->json([
            'status' => 'success',
            'path' => $storagePath,
            'avatar' => auth()->user()->avatar,
        ]);
    }

    public function uploadPlaceholderAvatar(Request $request)
    {
        $fullPath = env('APP_URL') . $request->avatar_path;
        auth()->user()->update(['avatar_path' => $fullPath]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function deleteAccount()
    {
        auth()->user()->delete();

        return response()->json(200);
    }

    public function viewQRCode()
    {
        $qrCode = UserQRCode::generate(auth()->id());

        return response()->json([
            'identifier' => $qrCode->identifier,
            'image' => $qrCode->image,
        ]);
    }

    public function updateBacsDetails(Request $request)
    {
        $request->validate([
            'sortCode' => 'required',
            'accountNumber' => 'required',
            'addressOne' => 'required',
            'city' => 'required',
            'postCode' => 'required',
        ]);

        $stripe = new StripeClient('sk_test_51IQzRJKlaf0ZXKql9K197KQneuCbhLreLyCMZqgWBpUp7UyhdCuxs9TEcisIiOSjstOrh67ZE8meQKUrQTD01UX100HA6dUbxs');

        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'bacs_debit',
            'billing_details' => [
                'address' => [
                    'line1' => $request->input('addressOne'),
                    'city' => $request->input('city'),
                    'postal_code' => $request->input('postCode'),
                    'country' => 'GB',
                ],
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
            ],
            'bacs_debit' => [
                'account_number' => $request->input('accountNumber'),
                'sort_code' => $request->input('sortCode'),
            ],
        ]);

        StripePaymentMethod::where('user_id', auth()->id())
            ->where('type', 'bacs_debit')
            ->update(['default' => 0]);

        StripePaymentMethod::create([
            'user_id' => auth()->id(),
            'payment_method' => $paymentMethod->id,
            'default' => 1,
            'type' => 'bacs_debit',
        ]);

        return response()->json('done');
    }

    public function loadPausedMembership()
    {
        $membership = Subscription::where('user_id', auth()->id())->whereNotNull('pause_days')->first();
        return response()->json($membership);
    }

    public function pauseMembership()
    {
        $sub = auth()->user()->subscription;

        if ($sub !== null) {
            $daysTillExpiry = 8;
            $sub->update([
                'expires' => now(),
                'pause_days' => $daysTillExpiry,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function unpauseMembership(Request $request)
    {
        $sub = Subscription::find($request->subscriptionId);

        if ($sub !== null) {
            $sub->update([
                'expires' => now()->addDays($sub->pause_days)->format('Y-m-d H:i:s'),
                'pause_days' => 0,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function scanTabletQR(Request $request)
    {
        $this->validate($request, [
            'identifier' => 'required',
        ]);

        $code = QRCode::where('identifier', $request->identifier)->latest()->first();

        if ($code !== null) {
            $code->update([
                'scanned_by' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'invalid',
        ]);
    }

    public function canPurchaseIntroPack()
    {
        $purchase = CreditPackPurchase::where('credit_pack_id', 1)->where('user_id', auth()->id())->first();

        if ($purchase === null) {
            return response()->json([
                'available' => true,
            ]);
        }

        return response()->json([
            'available' => false,
        ]);
    }
}
