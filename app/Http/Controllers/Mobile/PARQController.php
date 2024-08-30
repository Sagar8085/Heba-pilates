<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\PARQ;
use Illuminate\Http\Request;

class PARQController extends Controller
{
    public function all()
    {
        $parqs = PARQ::where('user_id', auth()->user()->id)->with('creator')->latest()->get();
        return response()->json($parqs);
    }

    public function single(PARQ $parq)
    {
        $parq->creator = $parq->creator;
        return response()->json($parq);
    }


    public function savePARQResponse(Request $request)
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
            'created_by' => auth()->user()->id,
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
            'request' => $request->current_injuries,
        ]);
    }


}
