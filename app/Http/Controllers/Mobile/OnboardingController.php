<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BodyPartFocus;
use App\Models\CreditPack;
use App\Models\Focus;
use App\Models\Goal;
use App\Models\Member;
use App\Models\PARQ;
use App\Models\UserBodyPartFocus;
use App\Models\UserFocus;
use App\Models\UserGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function getGoals(): JsonResponse
    {
        $goals = Goal::all();
        return response()->json($goals);
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
        ]);

        PARQ::create([
            'user_id' => auth()->user()->id,
            'created_by' => auth()->user()->id,
            'current_injuries' => $request->current_injuries === 'yes' ? 1 : 0,
            'current_injuries_details' => $request->current_injuries_details,
            'taking_medication' => $request->taking_medication === 'yes' ? 1 : 0,
            'taking_medication_details' => $request->taking_medication_details,
            'advised_by_doctor' => $request->advised_by_doctor === 'yes' ? 1 : 0,
            'advised_by_doctor_details' => $request->advised_by_doctor_details,
            'currently_pregnant' => $request->currently_pregnant === 'yes' ? 1 : 0,
            'currently_pregnant_details' => $request->currently_pregnant_details,
            'contact_first_name' => $request->first_name,
            'contact_last_name' => $request->last_name,
            'contact_phone_number' => $request->phone_number,
            'contact_email' => $request->email,
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

    /**
     * Retrieve the promotional onboarding pack available for purchase.
     *
     * @param None
     *
     * @return Json
     */
    public function getPromoPack()
    {
        $creditPack = CreditPack::find(11);

        return response()->json([
            'allow_purchase' => true,
            // We can toggle this to false if we don't want to display the promo purchase - without having to update the app.
            'credit_pack' => $creditPack,
        ]);
    }

    /**
     * Save additional information against user details.
     *
     * @param None
     *
     * @return Json
     */
    public function saveAdditionalInformation(Request $request)
    {
        $request->validate([
            'pilates_experience' => 'required',
            'fitness_level' => 'required',
            'focuses' => 'required',
        ]);

        $member = Member::updateOrCreate(['user_id' => auth()->user()->id], [
            'pilates_experience' => $request->pilates_experience,
            'fitness_level' => $request->fitness_level,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function toggleFocus(Request $request)
    {
        $duplicate = UserBodyPartFocus::where('user_id', auth()->user()->id)->where('focus_id',
            $request->focus_id)->first();

        if ($duplicate !== null) {
            $duplicate->delete();
        } else {
            UserBodyPartFocus::create([
                'user_id' => auth()->user()->id,
                'focus_id' => $request->focus_id,
            ]);
        }

        $selected = UserBodyPartFocus::where('user_id', auth()->user()->id)->get()->pluck('focus_id')->toArray();

        return response()->json([
            'status' => 'success',
            'selected' => $selected,
        ]);
    }
}
