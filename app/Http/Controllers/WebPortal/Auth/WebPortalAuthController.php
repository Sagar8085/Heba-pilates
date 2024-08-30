<?php

namespace App\Http\Controllers\WebPortal\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ContentPreference;
use App\Models\EmailVerificationTokens;
use App\Models\OnDemandCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;


class WebPortalAuthController extends Controller
{
    /**
     * Validates the input for registration and creates the user in a three step process
     *
     *
     **/
    public function register(Request $request, $step)
    {
        $data = [];

        switch ($step) {
            case 'step-1':
                $request->validate([
                    'first_name' => 'required|string',
                    'last_name' => 'required|string',
                    'email' => 'required|string|email:rfc,dns|unique:users',
                    'gender' => 'required|string',
                    'dob_day' => 'required',
                    'dob_year' => 'required',
                ]);

                $data = $request->only('first_name', 'last_name', 'gender', 'dob', 'email');

                $dateOfBirth = $request->dob_year . '-' . $request->dob_month . '-' . $request->dob_day;

                $data['user'] = $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'gender' => $data['gender'],
                    'date_of_birth' => $dateOfBirth,
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(8)),
                ]);

                // TODO come back and place this in email, remove it from being passed back to js
                $token = $data['token'] = Helper::generateToken($data['email']);

                $user->makeVisible('api_token');
                $bearer = $user->api_token;

                $emailVerificatioToken = EmailVerificationTokens::create([
                    'user_id' => $user->id,
                    'token' => $token,
                ]);

                //TODO SEND SES Email
                $user->sendWelcomeEmail();

                $data['success'] = true;
                $data['status'] = 'success-step1';


                break;
            case 'step-2':
                $request->validate([
                    'token' => 'required',
                    'user_id' => 'required',
                ]);

                if (EmailVerificationTokens::where('user_id', $request->user_id)->where('token',
                    $request->token)->first()) {
                    $data['status'] = 'success-step2';
                }
                break;
            case 'step-3':
                $request->validate([
                    // 'password'=> 'required|confirmed|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
                    'password' => 'required|confirmed|min:4',
                ]);

                $user = User::find($request->user_id);
                $user->password = Hash::make($request->password);
                $user->save();

                $user = User::find($request->user_id);
                $user->makeVisible('api_token');

                $paymentType = $request->subscription['payment_type'];

                if ($paymentType == 'monthly') {
                    Stripe::setApiKey(config('services.stripe.secret'));

                    $session = Session::create([
                        'payment_method_types' => ['bacs_debit'],
                        'line_items' => [
                            [
                                'price' => $request->subscription['price_id'],
                                'quantity' => 1,
                            ],
                        ],
                        'mode' => 'payment',
                        'payment_intent_data' => [
                            'setup_future_usage' => 'off_session',
                        ],
                        'success_url' => env('APP_URL') . '/membership/' . $request->subscription['identifier'] . '/bacs/success?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => env('APP_URL') . '/membership/' . $request->subscription['identifier'] . '/bacs/cancel',
                    ]);
                } else {
                    Stripe::setApiKey(config('services.stripe.secret'));

                    $session = Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [
                            [
                                'price' => $request->subscription['price_id'],
                                'quantity' => 1,
                            ],
                        ],
                        'mode' => 'payment',
                        'success_url' => env('APP_URL') . '/credit-packs/' . $request->subscription['credit_pack_id'] . '/purchase/success?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => env('APP_URL') . '/credit-packs/' . $request->subscription['credit_pack_id'] . '/purchase/cancel',
                    ]);
                }

                return response()->json([
                    'status' => 'success-step3',
                    'checkout_session' => $session,
                    'user' => $user,
                ]);

                break;
            case 'step-4':

                break;
        }

        return response()->json($data, 200);
    }

    public function login(Request $request): JsonResponse
    {
        $passed = false;
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();

        if (Auth::attempt($credentials)) {
            $user->makeVisible('api_token');
            $passed = true;
        }

        return response()->json([
            'passed' => $passed,
            'user' => $user,
            'status' => 'success',
        ]);
    }

    public function forgot(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        /**
         * There is no account with this email.
         */
        if ($user === null) {
            return response()->json([
                'status' => 'no-account',
            ]);
        }

        $user->sendResetPasswordEmail();

        return response()->json([
            'status' => 'sent',
        ]);
    }

    public function confirm(Request $request): JsonResponse
    {
        $token = $request->token;
        $emailVerificationToken = EmailVerificationTokens::where('token', '=', $token)->first();

        $user = User::find($emailVerificationToken->user_id);
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->save();
        $user->sendWelcomeEmail();

        $emailVerificationToken->confirmed = 1;
        $emailVerificationToken->save();

        return response()->json($user);
    }

    public function contentPreferences(): JsonResponse
    {
        $categories = OnDemandCategory::get();

        $cats = collect();

        foreach ($categories as $category) {
            $cats->push([
                'label' => $category->name,
                'value' => $category->id,
            ]);
        }

        return response()->json($cats);
    }

    public function updateContentPreferences(Request $request)
    {
        $user = User::where('api_token', $request->bearer)->first();

        $preference = ContentPreference::where('user_id', $user->id)->where('category_id',
            $request->preference)->first();

        if ($preference === null) {
            ContentPreference::create([
                'user_id' => $user->id,
                'category_id' => $request->preference,
            ]);
        } else {
            $preference->delete();
        }

        return response()->json(200);
    }
}
