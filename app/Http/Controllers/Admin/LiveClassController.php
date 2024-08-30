<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenTok\OpenTok;

class LiveClassController extends Controller
{
    protected $apiKey = '47155174';
    protected $apiSecret = '78ad5f7f7e967192d8fee67385d723875e29a5c9';

    /**
     * Display view for streaming a live class.
     *
     * @param App\Models\LiveClass $liveclass
     *
     * @return View
     */
    public function publishLiveClassStream(LiveClass $liveclass)
    {
        $token = '';
        $sessionId = '';
        $type = 'instructor';

        $opentok = new OpenTok($this->apiKey, $this->apiSecret);

        /**
         * If live class room token is empty, create a new room token and assign.
         */
        if ($liveclass->room_token == '') {
            $opentokSession = $opentok->createSession();
            $roomToken = $opentokSession->getSessionId();

            $liveclass->update([
                'room_token' => $roomToken,
            ]);
        }

        if ($type === 'member') {
            $data = 'type=member';
        } else {
            $data = 'type=trainer';
        }

        if ($liveclass->room_token != '') {
            $opentok = new OpenTok($this->apiKey, $this->apiSecret);
            $token = $opentok->generateToken($liveclass->room_token, [
                'data' => $data,
            ]);
            $sessionId = $liveclass->room_token;

            return view('admin.liveclass', compact('token', 'sessionId', 'type'));
        }

        echo 'Nothing To See Here';
        die;
    }

    /**
     * Store a new live class.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'duration' => 'required',
            'instructor_id' => 'required',
            'date' => 'required',
            'time_hour' => 'required',
            'time_minute' => 'required',
            'reformer' => 'required',
        ]);

        $carbonDate = Carbon::parse($request->date);

        $dateTime = $carbonDate->format('Y-m-d') . ' ' . $request->time_hour . ':' . $request->time_minute . ':00';

        $liveclass = LiveClass::create([
            'category_id' => $request->category_id,
            'instructor_id' => $request->instructor_id,
            'duration' => $request->duration,
            'datetime' => $dateTime,
            'reformer' => $request->reformer,
        ]);

        return response()->json($liveclass);
    }

    /**
     * Fetch a list of instructors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function instructors()
    {
        $instructors = User::onlyTrainers()->get();

        return response()->json($instructors);
    }

    /**
     * Load a single live class resource.
     *
     * @param \App\Models\LiveClass $liveclass
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(LiveClass $liveclass)
    {
        $liveclass->category = $liveclass->category;
        $liveclass->instructor = $liveclass->instructor;
        $liveclass->bookings = $liveclass->bookings;

        return response()->json($liveclass);
    }

    public function library()
    {
        $classes = LiveClass::with('category')->where('datetime', '>=',
            Carbon::now()->format('Y-m-d') . ' 00:00:00')->paginate(25);
        return response()->json($classes);
    }
}
