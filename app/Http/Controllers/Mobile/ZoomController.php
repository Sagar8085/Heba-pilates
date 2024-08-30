<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ZoomMeetingHelperMobile;
use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\TrainerZoomIds;
use Illuminate\Http\Request;


class ZoomController extends Controller
{
    use ZoomMeetingHelperMobile;

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

    // Checks to see if the traienr has a zoom ID set, returns true if yes, false if no
    public function checkTrainerHasZoomID(Trainer $trainer)
    {
        $hasZoom = TrainerZoomIds::where('trainer_id', $trainer->id)->first();

        if ($hasZoom) {
            return response()->json(true);
        }

        return response()->json(false);
    }
}
