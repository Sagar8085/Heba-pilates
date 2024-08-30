<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberPackage;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    public function singleMemberPackage(MemberPackage $memberpackage): JsonResponse
    {
        $memberpackage = MemberPackage::where('id', $memberpackage->id)
            ->with('package', 'trainer.profile', 'sessions.trainer.profile')
            ->first();

        return response()->json($memberpackage);
    }

    public function all(): JsonResponse
    {
        $packages = Package::all();

        return response()->json($packages, 200);
    }

    public function trainerPackages(User $user): JsonResponse
    {
        return response()->json($user->packages);
    }

    public function purchasedPackages(): JsonResponse
    {
        $member = Member::where('user_id', '=', auth()->user()->id)->first();
        return response()->json($member->purchasedPackages);
    }
}
