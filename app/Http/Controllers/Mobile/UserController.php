<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ContentPreference;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\MarketingPreference;
use App\Models\Member;
use App\Models\OnDemandCategory;
use App\Models\PushToken;
use App\Models\QRCode;
use App\Models\ReformerBooking;
use App\Models\UserQRCode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function me(): JsonResponse
    {
        $user = auth()->user();
        $user->subscription = $user->subscription;
        return response()->json($user);
    }

    public function updateName(Request $request): JsonResponse
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        auth()->user()->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email,' . auth()->user()->email . ',email',
            'password' => 'required',
        ]);

        /**
         * Check users password is correct.
         */
        if (Auth::guard('web')->attempt(['email' => auth()->user()->email, 'password' => $request->password])) {
            auth()->user()->update([
                'email' => $request->email,
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'failure',
            'message' => 'Your password is incorrect.',
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        /**
         * Check users password is correct.
         */
        if (Auth::guard('web')->attempt(['email' => auth()->user()->email, 'password' => $request->current_password])) {
            auth()->user()->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'failure',
            'message' => 'Your password is incorrect.',
        ]);
    }

    /**
     * Update User's personal fitness details
     **/
    public function updateFitness(Request $request)
    {
        $member = Member::where('user_id', auth()->user()->id)->first();
        if (!$member) {
            $member = Member::create([
                'user_id' => auth()->user()->id,
                'fitness_goal' => $request->focus,
                'height' => $request->height,
                'weight' => $request->weight,
                'bmr' => $request->bmr,
                'daily_calory_goal' => $request->targetCals,
            ]);
        } else {
            $member->fitness_goal = $request->focus;
            $member->height = $request->height;
            $member->weight = $request->weight;
            $member->bmr = $request->bmr;
            $member->daily_calory_goal = $request->targetCals;
            $member->save();
        }

        return response()->json($member);
    }

    public function storePushToken(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'os' => 'required',
            'device_id' => 'required',
        ]);

        $token = PushToken::create([
            'user_id' => auth()->user()->id,
            'token' => $request->token,
            'os' => $request->os,
            'device_id' => $request->device_id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function myReservations()
    {
        $reservations = ReformerBooking::select('reformer_bookings.*', 'reformers.gym_id')
            ->where('datetime', '>=', Carbon::now()->format('Y-m-d') . ' 00:00:00')
            ->where('user_id', auth()->user()->id)
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->with('reformer.gym')
            ->orderBy('reformer_bookings.datetime', 'ASC')
            ->get();

        return response()->json($reservations);
    }

    public function scanTabletQR(Request $request)
    {
        $this->validate($request, [
            'identifier' => 'required',
        ]);

        \Log::debug('Scanning Tablet QR: ' . $request->identifier);

        $code = QRCode::where('identifier', $request->identifier)->latest()->first();

        if ($code !== null) {
            \Log::debug('Success: ' . $request->identifier);

            $code->update([
                'scanned_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        }

        \Log::debug('Failure: ' . $request->identifier);

        return response()->json([
            'status' => 'invalid',
        ]);
    }

    public function myCreditPacks()
    {
        $purchases = CreditPackPurchase::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->where('studio_credits', '>', 0)
            ->where(function ($query) {
                $query->where('expires', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('expires');
            })
            ->with('pack')
            ->get();

        return response()->json($purchases);
    }

    public function availableCreditPacks()
    {
        $introPurchase = CreditPackPurchase::where('user_id', auth()->user()->id)->whereIn('credit_pack_id',
            [1, 11])->first();

        if (auth()->user()->subscription) {
            $purchasablePacks = [14, 12, 13];
        } else {
            $purchasablePacks = [2, 12, 13];
        }

        if ($introPurchase === null) {
            $creditPacks = CreditPack::orderBy('price', 'ASC')->whereIn('id', $purchasablePacks)->orWhere('id',
                11)->get();
        } else {
            $creditPacks = CreditPack::orderBy('price', 'ASC')->whereIn('id', $purchasablePacks)->get();
        }

        return response()->json($creditPacks);
    }

    public function avatarPresets()
    {
        $avatarPaths = [
            env('APP_URL') . '/images/avatars/avatar-1.png',
            env('APP_URL') . '/images/avatars/avatar-2.png',
            env('APP_URL') . '/images/avatars/avatar-3.png',
            env('APP_URL') . '/images/avatars/avatar-4.png',
            env('APP_URL') . '/images/avatars/avatar-5.png',
            env('APP_URL') . '/images/avatars/avatar-6.png',
        ];

        return response()->json($avatarPaths);
    }

    public function updateAvatarPreset(Request $request)
    {
        auth()->user()->update([
            'avatar_path' => $request->avatar_path,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function contentPreferences(): JsonResponse
    {
        $categories = OnDemandCategory::get();
        $contentPreferences = ContentPreference::where('user_id',
            auth()->user()->id)->get()->pluck('category_id')->toArray();

        foreach ($categories as $category) {
            if (in_array($category->id, $contentPreferences)) {
                $category->selected = true;
            } else {
                $category->selected = false;
            }
        }

        return response()->json($categories);
    }

    public function updateContentPreferences(Request $request): JsonResponse
    {
        $this->validate($request, [
            'preference_id' => 'required',
        ]);

        $preference = ContentPreference::where('user_id', auth()->user()->id)
            ->where('category_id', $request->preference_id)
            ->first();

        if ($preference !== null) {
            $preference->delete();
        } else {
            ContentPreference::create([
                'user_id' => auth()->user()->id,
                'category_id' => $request->preference_id,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function notificationPreferences(): JsonResponse
    {
        $preferences = MarketingPreference::where('member_id', auth()->user()->id)->first();

        if ($preferences == null) {
            $preferences = collect([
                'account' => 0,
                'new_content' => 0,
                'bookings' => 0,
                'marketing' => 0,
            ]);
        }

        return response()->json($preferences);
    }

    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $this->validate($request, [
            'account' => 'required',
            'new_content' => 'required',
            'bookings' => 'required',
            'marketing' => 'required',
        ]);

        $preferences = MarketingPreference::where('member_id', auth()->user()->id)->first();

        if ($preferences !== null) {
            $preferences->update([
                'account' => $request->account ? 1 : 0,
                'new_content' => $request->new_content ? 1 : 0,
                'bookings' => $request->bookings ? 1 : 0,
                'marketing' => $request->marketing ? 1 : 0,
            ]);
        } else {
            $preferences = MarketingPreference::create([
                'member_id' => auth()->user()->id,
                'account' => $request->account ? 1 : 0,
                'new_content' => $request->new_content ? 1 : 0,
                'bookings' => $request->bookings ? 1 : 0,
                'marketing' => $request->marketing ? 1 : 0,
            ]);
        }
        return response()->json($preferences);
    }

    public function deleteAccount(): JsonResponse
    {
        auth()->user()->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function getQRCode(): JsonResponse
    {
        $qrCode = UserQRCode::generate(auth()->user()->id);

        return response()->json($qrCode);
    }

    public function updateMemberTrackingTransparency(Request $request)
    {
        $member = Member::where('user_id', auth()->user()->id)->first();

        if ($member === null) {
            $member = Member::create([
                'user_id' => auth()->user()->id,
            ]);
        }

        $member->update([
            'tracking_enabled' => $request->tracking_enabled,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
