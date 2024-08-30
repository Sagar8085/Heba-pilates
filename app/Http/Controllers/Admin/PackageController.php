<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Validate and store a new package.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'credits' => 'required',
            'session_length' => 'required',
            'description' => 'required|min:200',
        ]);

        $package = Package::create([
            'name' => $request->name,
            'price' => $request->price,
            'credits' => $request->credits,
            'session_length' => $request->session_length,
            'description' => $request->description,
        ]);

        $user = User::where('id', $request->user_id)->first();

        $user->packages()->syncWithoutDetaching($package->id);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
