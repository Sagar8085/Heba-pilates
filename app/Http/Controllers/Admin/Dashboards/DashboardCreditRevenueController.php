<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SubscriptionTier;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardCreditRevenueController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = new \stdClass();
        $stats->today = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\CreditPackPurchase')->createdToday()->sum('value'), 2);
        $stats->week = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\CreditPackPurchase')->createdThisWeek()->sum('value'), 2);
        $stats->month = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\CreditPackPurchase')->createdMonth(date('Y'), date('m'))->sum('value'), 2);
        $stats->year = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\CreditPackPurchase')->createdYear(date('Y'))->sum('value'), 2);

        return response()->json($stats);
    }

    /**
     * Load revenue stats for previous 30 days.
     *
     * @param None
     *
     * @return Json
     */
    public function thirtyDayTrend()
    {
        $data = collect();
        $legend = collect();

        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateName = Carbon::now()->subDays($i)->format('d M');
            $legend->push($dateName);

            $startDate = Carbon::parse($date)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->format('Y-m-d') . ' 23:59:59';

            /**
             * Fetch the revenue for this data point.
             */
            $revenue = (Order::where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('orderable_type', 'App\Models\CreditPackPurchase')
                    ->sum('value') / 100);

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

    public function tierPerformances()
    {
        $stats = collect();
        $legend = collect();
        $data = collect();

        $tiers = SubscriptionTier::get();

        $groups = collect([
            ['name' => 'Intro Pack', 'group' => 'intro-pack', 'ids' => [1]],
            ['name' => '1 Session', 'group' => 'single-session', 'ids' => [2]],
            ['name' => '10 Sessions', 'group' => 'ten-sessions', 'ids' => [3, 9]],
            ['name' => '30 Sessions', 'group' => 'thirty-sessions', 'ids' => [4, 10]],
            ['name' => '1 Visit £1', 'group' => 'one-pound-visit', 'ids' => [5]],
            ['name' => 'Unlimited Promo', 'group' => 'unlimited-promo', 'ids' => [11]],
        ]);

        foreach ($groups as $group) {
            $stats->push([
                'name' => $group['name'],
                'group' => $group['group'],
                'count' => number_format(Order::select('orders.*')->createdMonth(date('Y'),
                    date('m'))->where('orderable_type', 'App\Models\CreditPackPurchase')->join('credit_packs_purchases',
                    'credit_packs_purchases.id',
                    'orders.orderable_id')->whereIn('credit_packs_purchases.credit_pack_id', $group['ids'])->count()),
                'sum' => number_format(Order::select('orders.*')->createdMonth(date('Y'),
                        date('m'))->where('orderable_type',
                        'App\Models\CreditPackPurchase')->join('credit_packs_purchases', 'credit_packs_purchases.id',
                        'orders.orderable_id')->whereIn('credit_packs_purchases.credit_pack_id',
                        $group['ids'])->sum('value') / 100, 2),
            ]);

            $legend->push($group['group']);
            $data->push(number_format(Order::select('orders.*')->createdMonth(date('Y'),
                date('m'))->where('orderable_type', 'App\Models\CreditPackPurchase')->join('credit_packs_purchases',
                'credit_packs_purchases.id', 'orders.orderable_id')->whereIn('credit_packs_purchases.credit_pack_id',
                $group['ids'])->count()));
        }

        return response()->json([
            'table' => $stats,
            'graph' => [
                'legend' => $legend,
                'data' => $data,
            ],
        ]);
    }
}
