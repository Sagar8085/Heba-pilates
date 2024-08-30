<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contracts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ContractsController extends Controller
{


    /**
     * Process Contract File and store in s3
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadContract(Request $request)
    {
        $contract = '';
        $user = auth()->user();
        if ($request->year && $request->month && $request->day) {
            $expires = $request->year . '-' . $request->month . '-' . $request->day;
        } else {
            $expires = null;
        }
        $request->validate([
            'name' => 'required',
            // 'file' => 'required'
        ]);
        $contract = Contracts::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'expires' => $expires,
            'created_by' => $user->id,
        ]);

        return response()->json($contract);

    }

    public function uploadContractFile(Request $request)
    {
        $contract = '';
        $user = auth()->user();

        if ($file = $request->file('file')) {
            $name = $file->getClientOriginalName();
            $file_path = Storage::disk('s3')->putFile('contracts', $file, 'public');
            $contract = Contracts::where('id', $request->id)->update([
                'path' => $file_path,
            ]);

        } else {

            $contract = array('No Files uploaded');

        }


        return response()->json([
            'contract' => $contract,
            'message' => $request->id,
        ]);

    }

    /**
     * Delete Contract
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteContract(Request $request)
    {

        $user = auth()->user();
        $contract = Contracts::where('id', $request->id)->delete();

        return response()->json([
            'contract' => $contract,
            'message' => $request->id,
        ]);
    }

    /**
     * Edit Contract
     * @param Request $request
     * @param \App\Models\Contracts $contracts
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function editContract(Request $request, Contracts $contracts)
    {

        $user = auth()->user();
        $expires = $request->year . '-' . $request->month . '-' . $request->day;
        $contract = Contracts::where('id', $request->id)->update([
            'expires' => $expires,
        ]);;


        return response()->json($contract);
    }

}
