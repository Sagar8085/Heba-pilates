<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\LiveClassBooking;
use App\Models\OnDemandView;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class MainDashboardController extends Controller
{
    public function ondemandStats(): JsonResponse
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        $totalViews = OnDemandView::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count();

        $topVideo = OnDemandView::select('on_demand_id', \DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('on_demand_id')
            ->with('class')
            ->orderByDesc('total')
            ->first();

        $trendGraph = $this->onDemandGraph();

        return response()->json([
            'totalViews' => $totalViews,
            'topVideo' => $topVideo,
            'trendGraph' => $trendGraph,
        ]);
    }

    private function onDemandGraph(): JsonResponse
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        $data = collect();
        $legend = collect();

        /**
         * Loop through the previous 30 days and calculate the number of views for this category.
         */
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateName = Carbon::now()->subDays($i)->format('jS');
            $legend->push($dateName);

            $startDate = Carbon::parse($date)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->format('Y-m-d') . ' 23:59:59';

            $revenue = OnDemandView::select('user_id')
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->get()->count();

            $data->push($revenue);
        }

        /**
         * Calculate the total sum of paid revenue for today.
         * Reverse the order of the data so that the most recent day is on the right.
         */
        $sum = $data->sum();
        $data = $data->reverse()->values()->all();
        $legend = $legend->reverse()->values()->all();

        return [
            'sum' => $sum,
            'data' => $data,
            'legend' => $legend,
        ];
    }

    public function liveStats(): JsonResponse
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        $totalBookings = LiveClassBooking::where('created_at', '>=', $startDate)->where('created_at', '<=',
            $endDate)->count();
        $uniqueBookings = LiveClassBooking::where('created_at', '>=', $startDate)->where('created_at', '<=',
            $endDate)->groupBy('member_id')->get()->count();

        $trendGraph = $this->liveGraph();

        return response()->json([
            'totalBookings' => $totalBookings,
            'uniqueBookings' => $uniqueBookings,
            'trendGraph' => $trendGraph,
        ]);
    }

    private function liveGraph(): JsonResponse
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        $data = collect();
        $legend = collect();

        /**
         * Loop through the previous 30 days and calculate the number of views for this category.
         */
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateName = Carbon::now()->subDays($i)->format('jS');
            $legend->push($dateName);

            $startDate = Carbon::parse($date)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->format('Y-m-d') . ' 23:59:59';

            $revenue = LiveClassBooking::select('member_id')
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->get()->count();

            $data->push($revenue);
        }

        /**
         * Calculate the total sum of paid revenue for today.
         * Reverse the order of the data so that the most recent day is on the right.
         */
        $sum = $data->sum();
        $data = $data->reverse()->values()->all();
        $legend = $legend->reverse()->values()->all();

        return [
            'sum' => $sum,
            'data' => $data,
            'legend' => $legend,
        ];
    }
}
