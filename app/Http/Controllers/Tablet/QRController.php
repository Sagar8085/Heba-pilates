<?php

namespace App\Http\Controllers\Tablet;

use App\Http\Controllers\Controller;
use App\Models\QRCode;
use App\Models\User;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QRCodeProvider;

class QRController extends Controller
{
    public function generateLoginQR()
    {
        $identifier = time();

        $image = base64_encode(QRCodeProvider::format('png')->backgroundColor(250, 250, 250)->color(0, 0,
            0)->size(250)->generate($identifier));

        $qrCode = QRCode::create([
            'identifier' => $identifier,
            'expires' => Carbon::now()->addSeconds(60)->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'identifier' => $identifier,
            'image' => $image,
        ]);
    }

    public function checkLoginQR($identifier)
    {
        $qrCode = QRCode::where('identifier', $identifier)->latest()->first();

        if ($qrCode->scanned_by !== null) {
            $user = User::find($qrCode->scanned_by);
            $user->makeVisible('api_token');

            return response()->json([
                'status' => 'accepted',
                'user' => $user,
            ]);
        }

        return response()->json([
            'status' => 'pending',
        ]);
    }
}
