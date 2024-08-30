<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionZoomLink;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * All sessions report.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $sessions = Session::latest()->with('member', 'trainer')->paginate(50);

        return response()->json($sessions);
    }

    /**
     * Load a single session.
     *
     * @param \App\Models\Session $session
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Session $session)
    {
        $session->member = $session->member;
        $session->trainer = $session->trainer;
        $session->trainer->profile = $session->trainer->profile;
        $session->package = $session->package;

        return response()->json($session);
    }

    /**
     * Cancel a session.
     *
     * @param \App\Models\Session $session
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Session $session)
    {
        $session->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Accept a session.
     * @param \App\Models\Session $session
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function accept(Session $session)
    {
        $session->update([
            'status' => 'upcoming',
        ]);

        return response()->json([
            'status' => 'confirmed',
            'message' => 'This session has now been confirmed and the member has been notified.',
        ]);
    }

    /**
     * Decline a session.
     *
     * @param \App\Models\Session $session
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function decline(Session $session)
    {
        $session->update([
            'status' => 'declined',
            'cancelled_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'declined',
            'message' => 'This session has been declined, the member has been notified and their credit has been added back to their account.',
        ]);
    }

    /**
     * Stores the created zoom link
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeZoomLink(Request $request)
    {
        $sessionZoom = SessionZoomLink::where('session_id', $request->session_id)->first();
        $session = Session::where('id', $request->session_id)->first();

        if (!$sessionZoom) {
            $duration = $session->length + 15;
            $sessionZoom = SessionZoomLink::create([
                "session_id" => $request->session_id,
                "link" => $request->link,
                "expires" => date('Y-m-d H:i:s', strtotime("+{$duration} minutes", strtotime($session->datetime))),
            ]);
        } else {
            $sessionZoom->link = $request->link;
            $duration = $session->length + 15;
            $sessionZoom->expires = date('Y-m-d H:i:s',
                strtotime("+{$duration} minutes", strtotime($session->datetime)));
            $sessionZoom->save();
        }

        return response()->json($sessionZoom);
    }

    /**
     * gets the stored zoom link
     *
     * @param \App\Models\Session $session
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleZoomLink(Session $session, Request $request)
    {
        $sessionZoom = SessionZoomLink::where('session_id', $session->id)->first();

        return response()->json($sessionZoom);
    }
}
