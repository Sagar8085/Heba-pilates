<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserQRCode;

class DoorAccessController extends Controller
{
    public function liveLog()
    {
        $accessLog = UserQRCode::orderBy('scanned_at',
            'DESC')->whereNotNull('scanned_at')->take(20)->with('user.subscription', 'gym')->get();
        $latest = UserQRCode::orderBy('scanned_at',
            'DESC')->whereNotNull('scanned_at')->take(1)->with('user.subscription', 'gym')->get();

        return response()->json([
            'latest' => $latest,
            'all' => $accessLog,
        ]);
    }

    public function all()
    {
        $accessLog = UserQRCode::orderBy('scanned_at', 'DESC')->whereNotNull('scanned_at')->with('user.subscription',
            'gym')->paginate(25);

        return response()->json($accessLog);
    }
}
