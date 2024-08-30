<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassBooking;
use App\Models\LiveClassCategory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LiveClassPenetrationController extends Controller
{
    public function previousThirtyDays(LiveClassCategory $category): JsonResponse
    {
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

            $revenue = LiveClassBooking::select('live_classes_bookings.member_id')
                ->join('live_classes', 'live_classes.id', 'live_classes_bookings.liveclass_id')
                ->where('live_classes.category_id', $category->id)
                ->where('live_classes_bookings.created_at', '>=', $startDate)
                ->where('live_classes_bookings.created_at', '<=', $endDate)
                ->groupBy('live_classes_bookings.member_id')->get()->count();

            $data->push($revenue);
        }

        /**
         * Calculate the total sum of paid revenue for today.
         * Reverse the order of the data so that the most recent day is on the right.
         */
        $sum = number_format($data->sum(), 2);
        $data = $data->reverse()->values()->all();
        $legend = $legend->reverse()->values()->all();

        return response()->json([
            'sum' => $sum,
            'data' => $data,
            'legend' => $legend,
        ]);
    }

    public function topClasses(LiveClassCategory $category): JsonResponse
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        $classIds = LiveClass::where('datetime', '>=', $startDate)
            ->where('datetime', '<=', $endDate)
            ->where('category_id', $category->id)
            ->get()->pluck('id')->toArray();

        $classes = LiveClassBooking::select('liveclass_id', \DB::raw('count(*) as total'))
            ->whereIn('liveclass_id', $classIds)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('liveclass_id')
            ->with('class')
            ->orderByDesc('total')
            ->get();

        return response()->json($classes);
    }
}
