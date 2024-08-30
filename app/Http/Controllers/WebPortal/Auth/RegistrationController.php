<?php

namespace App\Http\Controllers\WebPortal\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\BodyPartFocus;
use App\Models\Focus;
use App\Models\Goal;
use App\Models\MarketingPreference;
use App\Models\Member;
use App\Models\PARQ;
use App\Models\User;
use App\Models\UserBodyPartFocus;
use App\Models\UserFocus;
use App\Models\UserGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phone_number' => 'required',
            'dob_day' => 'required | numeric | min:1 | max:31',
            'dob_month' => 'required | numeric | min:1 | max:12',
            'dob_year' => 'required | numeric | min:1900 | max:2021',
            'gender' => 'required',
            'home_studio_id' => 'required',
        ]);

        $user = User::create([
            'role_id' => 4, // This role ID is for members only
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->dob_year . '-' . $request->dob_month . '-' . $request->dob_day,
            'gender' => $request->gender,
        ]);

        $marketing = MarketingPreference::updateOrCreate(['member_id' => $user->id], [
            'member_id' => $user->id,
            'heard_about_us' => $request->marketing_hear_about_us,
        ]);

        $member = Member::updateOrCreate(
            ['user_id' => $user->id],
            ['home_studio_id' => $request->home_studio_id]
        );

        SendEmailJob::dispatch($user, 'emails.user.profile-welcome-email', 'Welcome to Heba Pilates',
            ['user' => $user])->onQueue('account-notifications');

        $user->makeVisible('api_token');

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'member' => $member,
            'marketing' => $marketing,
        ]);
    }

    public function getGoals(): JsonResponse
    {
        return response()->json(Goal::all());
    }

    public function setGoals(Request $request): JsonResponse
    {
        UserGoal::where('user_id', auth()->user()->id)->delete();

        foreach ($request->goals as $goalId) {
            UserGoal::create([
                'user_id' => auth()->user()->id,
                'goal_id' => $goalId,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function getFocuses(): JsonResponse
    {
        $focuses = Focus::all();
        return response()->json($focuses);
    }

    public function setFocuses(Request $request): JsonResponse
    {
        UserFocus::where('user_id', auth()->user()->id)->delete();

        foreach ($request->focuses as $focusId) {
            UserFocus::create([
                'user_id' => auth()->user()->id,
                'focus_id' => $focusId,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function getBodyPartFocuses(): JsonResponse
    {
        $focuses = BodyPartFocus::all();
        return response()->json($focuses);
    }

    public function setBodyPartFocuses(Request $request): JsonResponse
    {
        UserBodyPartFocus::where('user_id', auth()->user()->id)->delete();

        foreach ($request->focuses as $focusId) {
            UserBodyPartFocus::create([
                'user_id' => auth()->user()->id,
                'focus_id' => $focusId,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function setFitnessLevel(Request $request): JsonResponse
    {
        $member = Member::where('user_id', auth()->user()->id)->first();

        if ($member === null) {
            $member = Member::create([
                'user_id' => auth()->user()->id,
            ]);
        }

        $member->update([
            'fitness_level' => $request->fitness_level,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function setPilatesExperience(Request $request): JsonResponse
    {
        $member = Member::where('user_id', auth()->user()->id)->first();

        if ($member === null) {
            $member = Member::create([
                'user_id' => auth()->user()->id,
            ]);
        }

        $member->update([
            'pilates_experience' => $request->pilates_experience,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function savePARQResponse(Request $request): JsonResponse
    {
        $request->validate([
            'current_injuries' => 'required',
            'taking_medication' => 'required',
            'advised_by_doctor' => 'required',
            'currently_pregnant' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'current_injuries_details' => 'required_if:current_injuries,yes',
            'taking_medication_details' => 'required_if:taking_medication,yes',
            'advised_by_doctor_details' => 'required_if:advised_by_doctor,yes',
            'currently_pregnant_details' => 'required_if:currently_pregnant,yes',
        ]);

        PARQ::create([
            'user_id' => auth()->user()->id,
            'current_injuries' => $request->current_injuries === 'yes' ? 1 : 0,
            'taking_medication' => $request->taking_medication === 'yes' ? 1 : 0,
            'advised_by_doctor' => $request->advised_by_doctor === 'yes' ? 1 : 0,
            'currently_pregnant' => $request->currently_pregnant === 'yes' ? 1 : 0,
            'contact_first_name' => $request->first_name,
            'contact_last_name' => $request->last_name,
            'contact_phone_number' => $request->phone_number,
            'contact_email' => $request->email,
            'current_injuries_details' => $request->current_injuries_details,
            'taking_medication_details' => $request->taking_medication_details,
            'advised_by_doctor_details' => $request->advised_by_doctor_details,
            'currently_pregnant_details' => $request->currently_pregnant_details,
            'created_by' => auth()->user()->id,
        ]);

        $member = Member::where('user_id', auth()->user()->id)->first();

        if ($member === null) {
            $member = Member::create([
                'user_id' => auth()->user()->id,
            ]);
        }

        $member->update([
            'onboarding_complete' => 1,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
