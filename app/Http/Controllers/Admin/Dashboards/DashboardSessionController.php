<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardSessionController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = new \stdClass();

        $stats->completed = number_format(Session::where('datetime', '>=',
            Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')
            ->where('datetime', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->whereIn('status', ['completed', 'noshow'])
            ->count());

        $stats->upcoming = number_format(Session::where('datetime', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('datetime', '<=', Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59')
            ->where('status', 'confirmed')
            ->count());

        $stats->pending = number_format(Session::where('datetime', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('datetime', '<=', Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59')
            ->where('status', 'pending')
            ->count());

        $stats->cancelled = number_format(Session::where('datetime', '>=',
            Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')
            ->where('datetime', '<=', Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59')
            ->where('status', 'cancelled')
            ->count());

        $stats->noshow = Session::where('datetime', '>=', Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')
            ->where('datetime', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('status', 'noshow')
            ->count();

        $stats->rating = number_format(Session::where('datetime', '>=',
            Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')
            ->where('datetime', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('status', 'completed')
            ->avg('rating'), 1);

        $stats->totalBookings = Session::where('datetime', '>=',
            Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')
            ->where('datetime', '<=', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'))
            ->count();

        $stats->average_sessions = ($stats->totalBookings / 4);

        if ($stats->completed > 0) {
            $stats->attendance = round(100 - ($stats->noshow / ($stats->completed / 100)));
        } else {
            $stats->attendance = 0;
        }

        return response()->json($stats);
    }

    public function bookings(): JsonResponse
    {
        $data = collect();
        $legend = collect();

        /**
         * Loop through the previous 6 months and calculate the paid revenue for each of those months.
         */
        for ($i = 0; $i <= 6; $i++) {
            $date = Carbon::now()->subMonths($i)->format('Y-m-d');
            $dateName = Carbon::now()->subMonths($i)->format('M');
            $year = Carbon::now()->subMonths($i)->format('y');
            $legend->push($dateName . ' ' . $year);

            $startDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d') . ' 23:59:59';

            $bookings = Session::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->count();

            $data->push($bookings);
        }

        /**
         * Calculate the total sum of paid revenue for today.
         * Reverse the order of the data so that the most recent day is on the right.
         */
        $sum = number_format($data->sum());
        $data = $data->reverse()->values()->all();
        $legend = $legend->reverse()->values()->all();

        return response()->json([
            'sum' => $sum,
            'data' => $data,
            'legend' => $legend,
        ]);
    }

    public function cancellations(): JsonResponse
    {
        $data = collect();
        $legend = collect();

        /**
         * Loop through the previous 12 months and calculate the paid revenue for each of those months.
         */
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i)->format('Y-m-d');
            $dateName = Carbon::now()->subMonths($i)->format('M');
            $year = Carbon::now()->subMonths($i)->format('y');
            $legend->push($dateName . ' ' . $year);

            $startDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d') . ' 23:59:59';

            $bookings = Session::where('cancelled_at', '>=', $startDate)
                ->where('cancelled_at', '<=', $endDate)
                ->where('status', 'cancelled')
                ->count();

            $data->push($bookings);
        }

        /**
         * Calculate the total sum of paid revenue for today.
         * Reverse the order of the data so that the most recent day is on the right.
         */
        $sum = number_format($data->sum());
        $data = $data->reverse()->values()->all();
        $legend = $legend->reverse()->values()->all();

        return response()->json([
            'sum' => $sum,
            'data' => $data,
            'legend' => $legend,
        ]);
    }
}
