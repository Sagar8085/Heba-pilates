<?php

namespace App\Http\Controllers\Mobile\v2;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function registerAccount(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phone_number' => 'required',
            'dob_day' => 'required|numeric|gte:1|lte:31',
            'dob_month' => 'required|numeric|gte:1|lte:12',
            'dob_year' => 'required|numeric',
            'gender' => 'required',
            'home_studio_id' => 'required',
        ]);

        $dob = $request->dob_year . '-' . $request->dob_month . '-' . $request->dob_day;

        $user = User::create([
            'role_id' => 4, // This role ID is for members only
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'date_of_birth' => $dob,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $member = Member::updateOrCreate(
            ['user_id' => $user->id],
            ['home_studio_id' => $request->home_studio_id]
        );

        $user->makeVisible('api_token');

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }
}
