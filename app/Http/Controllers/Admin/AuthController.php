<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationTokens;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Validate attempted user login.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $passed = false;
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->whereIn('role_id', [1, 2, 3])->first();

        if (!$user) {
            return response()->json([
                'passed' => false,
                'status' => 'unsucessful',
            ]);
        }

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

    /**
     * Send a forgot password email.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot(Request $request)
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

    public function invitation(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);

        $token = EmailVerificationTokens::where('token', $request->token)->first();

        if ($token !== null) {
            $user = User::where('id', $token->user_id)->first();

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            $user->makeVisible('api_token');

            $token->delete();

            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        }

        return response()->json([
            'status' => 'failure',
        ]);
    }
}
