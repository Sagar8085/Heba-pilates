<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Get trainer availability
     *
     * @param Request $request
     * @param User $user
     */
    public function availability(Request $request, User $user)
    {
        $week_start = Carbon::parse($request->week_start)->format('Y-m-d');
        $week_end = Carbon::parse($request->week_end)->format('Y-m-d');

        $weekStartRaw = Carbon::parse($request->week_start);
        $weekEndRaw = Carbon::parse($request->week_end);

        $availability = Availability::where('user_id', $user->id)
            ->where('start', '>=', $week_start . ' 00:00:00')
            ->where('end', '<=', $week_end . ' 23:59:59')
            ->get();

        $sessions = Session::with('member')
            ->where('trainer_id', $user->id)
            ->where('date', '>=', $week_start)
            ->where('date', '<=', $week_end)
            ->get();

        return response()->json([
            'availability' => $availability,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Save trainer availability
     *
     * @param Request $request
     * @param User $user
     */
    public function save_availability(Request $request, User $user)
    {
        if (Carbon::parse($request->start)->format('Y-m-d') != Carbon::parse($request->end)->format('Y-m-d')) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        $start = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $end = Carbon::parse($request->end)->format('Y-m-d H:i:s');

        /**
         * Get the starting hour.
         * Get the ending hour.
         */
        $startingHour = date('H', strtotime($start));
        $endingHour = date('H', strtotime($end));

        /**
         * Calc the duration of the slot in hours.
         */
        $duration = ($endingHour - $startingHour);

        $availability = Availability::create([
            'user_id' => $user->id,
            'date' => Carbon::parse($request->start)->format('Y-m-d'),
            'hours' => $duration,
            'start' => $start,
            'end' => $end,
        ]);

        return response()->json([
            'status' => 'success',
            'availability' => $availability,
        ]);
    }

    /**
     * Update an existing availability block
     *
     * @param Request $request
     * @param User $user
     */
    public function update_availability(Request $request, User $user, $availability_id)
    {
        if (Carbon::parse($request->start)->format('Y-m-d') != Carbon::parse($request->end)->format('Y-m-d')) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        $availability = Availability::find($availability_id);

        if ($availability !== null) {

            $availability->update([
                'end' => Carbon::parse($request->end)->setTimezone('UTC')->addHours(1)->format('Y-m-d H:i:s'),
            ]);

        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Remove an availability block
     *
     * @param Request $request
     * @param User $user
     */
    public function remove_availability(Request $request, User $user, $availability_id)
    {
        $availability = Availability::find($availability_id);

        if ($availability !== null) {

            // Are there sessions within this availability block?

            $availability->delete();

        }

        return response()->json([
            'status' => 'success',
        ]);
    }
}
