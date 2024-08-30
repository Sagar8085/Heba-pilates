<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionZoomLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function singleZoomLink(Session $session, Request $request): JsonResponse
    {
        $sessionZoom = SessionZoomLink::where('session_id', $session->id)->first();

        return response()->json($sessionZoom);
    }
}
