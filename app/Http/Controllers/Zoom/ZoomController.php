<?php

namespace App\Http\Controllers\Zoom;

use App\Helpers\ZoomMeetingHelper;
use App\Http\Controllers\Controller;
use App\Models\TrainerZoomIds;
use Illuminate\Http\Request;


class ZoomController extends Controller
{
    use ZoomMeetingHelper;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function show($id)
    {
        $meeting = $this->get($id);

    }

    public function store(Request $request)
    {
        return response()->json($this->create($request->all()));
    }

    public function update($meeting, Request $request)
    {
        return response()->json($this->update($meeting->zoom_meeting_id, $request->all()));

    }

    public function destroy(ZoomMeeting $meeting)
    {
        return response()->json($this->delete($meeting->id));

    }

    public function storeUserId(Request $request)
    {
        $zoomUserID = TrainerZoomIds::where('trainer_id', $request->trainer_id)->first();

        if ($zoomUserID) {
            $zoomUserID->zoom_user_id = $request->zoom_id;
            $zoomUserID->save();
        } else {
            $zoomUserID = TrainerZoomIds::create([
                "trainer_id" => $request->trainer_id,
                "zoom_user_id" => $request->zoom_id,
            ]);
        }

        return response()->json($zoomUserID);
    }

    public function getId($trainer)
    {
        $zoomUserID = TrainerZoomIds::where('trainer_id', $trainer)->first();
        return response()->json($zoomUserID);
    }
}
