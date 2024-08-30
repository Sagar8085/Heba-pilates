<?php

namespace App\Http\Controllers;

use App\Events\TurnstileQRScannedEvent;
use App\Models\UserQRCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoorAccessController extends Controller
{
    public function turnstileScanned(Request $request)
    {
        if ($request->identifier === 'HEBAGUIDEACCESSPASS') {
            \Log::debug('Opening Door for Heba Guide');
            return response()->json([
                'status' => 'valid',
            ]);
        }

        $qrCode = UserQRCode::where('identifier', $request->identifier)->isActive()->with('user')->first();

        if ($qrCode !== null) {

            $qrCode->update([
                'scanned_at' => Carbon::now(),
            ]);

            if (isset($request->gymId)) {
                $qrCode->update([
                    'gym_id' => $request->gymId,
                ]);
            } else {
                // Beaconsfield and Marlow have this setup, Windsor is the only one that doesn't so...
                // If the requests has no gymId then it must be Windsor.
                $qrCode->update([
                    'gym_id' => 1,
                ]);
            }

            event(new TurnstileQRScannedEvent($qrCode)); // when logs in

            return response()->json([
                'status' => 'valid',
            ]);
        }

        return response()->json([
            'status' => 'invalid',
        ]);
    }
}
