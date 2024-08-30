<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use App\Models\SubscriptionTier;
use Arr;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardRevenueController extends Controller
{

    public function totalRevenue(Request $request)
    {
        $chartUnit = $request->chartUnit;

        $reportDates = collect($request->reportDates);

        $compareDates = Arr::whereNotNull(collect($request->compareDates)
            ->flatten()
            ->all());

        $orders = Order::orderBy('id');

        $members = Member::orderBy('id');
        if ($gymId = $request->selectedLocation) {
            $orders = $orders->whereHas('member.memberProfile', function ($query) use ($gymId) {
                $query->where('home_studio_id', $gymId);
            });

            $members = $members->where('home_studio_id', $gymId);
        }

        $members = $members->count();

        $ordersCompare = with(clone $orders);

        if (!$reportDates->isEmpty() && $chartUnit === '') {
            $orders = $orders->whereBetween('created_at', [$request->reportDates[0], $request->compareDates[1]]);
        }

        if (!empty($compareDates) && !$chartUnit) {
            if ($fromDate = $request->compareDates['from']) {
                $ordersCompare = $ordersCompare->where('created_at', '>=', $fromDate);
            }

            $ordersCompare = $ordersCompare->where('created_at', '<=', $request->compareDates['to'] ?? Carbon::now());
        }

        $chartKeys = collect();
        $chartKeysCompare = collect();

        $totalRevenueReport = collect();
        $totalRevenueCompare = collect();

        $subscriptionRevenueReport = collect();
        $subscriptionRevenueCompare = collect();

        $creditPackRevenueReport = collect();
        $creditPackRevenueCompare = collect();

        $promoRevenueReport = collect();
        $promoRevenueCompare = collect();

        $meanChargeReport = collect();
        $meanChargeCompare = collect();

        /* Report dates */
        $carbonStart = Carbon::parse($request->reportDates['from'] ?? Carbon::now()->startOfYear());
        $carbonEnd = Carbon::parse($request->reportDates['to'] ?? Carbon::now());

        $purchaseTypes = with(clone $orders)->selectRaw('orderable_type, count(id) as total')
            ->whereBetween('created_at', [$carbonStart->format('Y-m-d H:i:s'), $carbonEnd->format('Y-m-d H:i:s')])
            ->groupBy('orderable_type')
            ->orderBy('total')
            ->withoutAppends()
            ->get();

        $purchaseTypeKeys = collect();
        $purchaseTypeReport = collect();

        $purchaseTypes->each(function ($item) use ($purchaseTypeKeys, $purchaseTypeReport) {
            $purchaseTypeKeys->push($item->orderable_type_human);
            $purchaseTypeReport->push($item->total);
        });

        if ($chartUnit === 'day') {

            $fallback = Carbon::now()->startOfWeek();
            if (!$request->reportDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInDays = $carbonStart->diffInDays($carbonEnd);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $currentDay = $carbonStart->copy()->addDays($i);

                $startOfDay = $currentDay->startOfDay()->format('Y-m-d H:i:s');
                $endOfDay = $currentDay->endOfDay()->format('Y-m-d H:i:s');

                $totalRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfDay, $endOfDay]);

                $subscriptionRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->whereNotNull('promo_code');

                $chartKeys->push($currentDay->format('Y-m-d'));

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueReport->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueReport->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueReport->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueReport->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeReport->push($avgPerGuest);
            }

        } elseif ($chartUnit === 'week') {
            $fallback = Carbon::now()->startOfMonth();
            if (!$request->reportDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInWeeks = $carbonStart->diffInWeeks($carbonEnd);
            for ($i = 0; $i <= $diffInWeeks; $i++) {
                $currentWeek = $carbonStart->copy()->addDays($i);

                $startOfWeek = $currentWeek->startOfWeek()->format('Y-m-d H:i:s');
                $endOfWeek = $currentWeek->endOfWeek()->format('Y-m-d H:i:s');

                $totalRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfWeek, $endOfWeek]);

                $subscriptionRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->whereNotNull('promo_code');

                $chartKeys->push($currentWeek->format('Y-m-d'));

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueReport->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueReport->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueReport->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueReport->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeReport->push($avgPerGuest);
            }
        } elseif ($chartUnit === 'month') {

            $fallback = Carbon::now()->startOfYear();
            if (!$request->reportDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInMonths = $carbonStart->diffInMonths($carbonEnd);
            for ($i = 0; $i <= $diffInMonths; $i++) {
                $currentMonth = $carbonStart->copy()->addMonths($i);

                $startOfMonth = $currentMonth->startOfMonth()->format('Y-m-d H:i:s');
                $endOfMonth = $currentMonth->endOfMonth()->format('Y-m-d H:i:s');

                if ($i === $diffInMonths) {
                    $endOfMonth = $carbonEnd->format('Y-m-d H:i:s');
                }


                $totalRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

                $subscriptionRevenueQuery = with(clone $orders)->whereBetween('created_at',
                    [$startOfMonth, $endOfMonth])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->whereNotNull('promo_code');

                $chartKeys->push($currentMonth->shortMonthName);

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueReport->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueReport->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueReport->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueReport->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeReport->push($avgPerGuest);
            }
        } elseif ($chartUnit === 'year') {

            $fallback = Carbon::now()->subYears(5)->startOfYear();
            if (!$request->reportDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInYears = $carbonStart->diffInYears($carbonEnd);
            for ($i = 0; $i <= $diffInYears; $i++) {
                $currentYear = $carbonStart->copy()->addYears($i);

                $startOfYear = $currentYear->startOfYear()->format('Y-m-d H:i:s');
                if ($startOfYear < $carbonStart) {
                    $startOfYear = $carbonStart->format('Y-m-d H:i:s');
                }

                $endOfYear = $currentYear->endOfYear()->format('Y-m-d H:i:s');

                $totalRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfYear, $endOfYear]);

                $subscriptionRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfYear, $endOfYear])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfYear, $endOfYear])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $orders)->whereBetween('created_at', [$startOfYear, $endOfYear])
                    ->whereNotNull('promo_code');

                $chartKeys->push($currentYear->year);

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueReport->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueReport->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueReport->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueReport->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeReport->push($avgPerGuest);
            }
        }

        /* Compare dates */
        $carbonStart = Carbon::parse($request->compareDates['from'] ?? Carbon::now()->startOfYear());
        $carbonEnd = Carbon::parse($request->compareDates['to'] ?? Carbon::now());

        $purchaseTypes = with(clone $orders)->selectRaw('orderable_type, count(id) as total')
            ->whereBetween('created_at', [$carbonStart->format('Y-m-d H:i:s'), $carbonEnd->format('Y-m-d H:i:s')])
            ->groupBy('orderable_type')
            ->orderBy('total')
            ->withoutAppends()
            ->get();

        $purchaseTypeCompareKeys = collect();
        $purchaseTypeCompareReport = collect();

        $purchaseTypes->each(function ($item) use ($purchaseTypeCompareKeys, $purchaseTypeCompareReport) {
            $purchaseTypeCompareKeys->push($item->orderable_type_human);
            $purchaseTypeCompareReport->push($item->total);
        });

        if ($chartUnit === 'day') {
            $fallback = Carbon::now()->startOfWeek();
            if (!$request->compareDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInDays = $carbonStart->diffInDays($carbonEnd);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $currentDay = $carbonStart->copy()->addDays($i);

                $startOfDay = $currentDay->startOfDay()->format('Y-m-d H:i:s');
                $endOfDay = $currentDay->endOfDay()->format('Y-m-d H:i:s');

                $totalRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at', [$startOfDay, $endOfDay]);

                $subscriptionRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfDay, $endOfDay])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfDay, $endOfDay])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->whereNotNull('promo_code');

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueCompare->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueCompare->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueCompare->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueCompare->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeCompare->push($avgPerGuest);
            }
        } elseif ($chartUnit === 'week') {
            $fallback = Carbon::now()->startOfMonth();
            if (!$request->compareDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInWeeks = $carbonStart->diffInWeeks($carbonEnd);
            for ($i = 0; $i <= $diffInWeeks; $i++) {
                $currentWeek = $carbonStart->copy()->addDays($i);

                $startOfWeek = $currentWeek->startOfWeek()->format('Y-m-d H:i:s');
                $endOfWeek = $currentWeek->endOfWeek()->format('Y-m-d H:i:s');

                $totalRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at', [$startOfWeek, $endOfWeek]);

                $subscriptionRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfWeek, $endOfWeek])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfWeek, $endOfWeek])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->whereNotNull('promo_code');

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueCompare->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueCompare->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueCompare->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueCompare->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeCompare->push($avgPerGuest);
            }
        } elseif ($chartUnit === 'month') {

            $fallback = Carbon::now()->startOfYear();
            if (!$request->compareDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInMonths = $carbonStart->diffInMonths($carbonEnd);
            for ($i = 0; $i <= $diffInMonths; $i++) {
                $currentMonth = $carbonStart->copy()->addMonths($i);

                $startOfMonth = $currentMonth->startOfMonth()->format('Y-m-d H:i:s');
                $endOfMonth = $currentMonth->endOfMonth()->format('Y-m-d H:i:s');

                if ($i === $diffInMonths) {
                    $endOfMonth = $carbonEnd->format('Y-m-d H:i:s');
                }

                $totalRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfMonth, $endOfMonth]);

                $subscriptionRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfMonth, $endOfMonth])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfMonth, $endOfMonth])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfMonth, $endOfMonth])
                    ->whereNotNull('promo_code');

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueCompare->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueCompare->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueCompare->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueCompare->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeCompare->push($avgPerGuest);
            }
        } elseif ($chartUnit === 'year') {

            $fallback = Carbon::now()->subYears(5)->startOfYear();
            if (!$request->compareDates['from']) {
                $carbonStart = $fallback;
            }

            $diffInYears = $carbonStart->diffInYears($carbonEnd);
            for ($i = 0; $i <= $diffInYears; $i++) {
                $currentYear = $carbonStart->copy()->addYears($i);

                $startOfYear = $currentYear->startOfYear()->format('Y-m-d H:i:s');
                if ($startOfYear < $carbonStart) {
                    $startOfYear = $carbonStart->format('Y-m-d H:i:s');
                }

                $endOfYear = $currentYear->endOfYear()->format('Y-m-d H:i:s');

                $totalRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at', [$startOfYear, $endOfYear]);

                $subscriptionRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfYear, $endOfYear])
                    ->where('orderable_type', 'App\Models\Subscription');

                $creditPackRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at',
                    [$startOfYear, $endOfYear])
                    ->where(function ($query) {
                        $query->where('orderable_type', 'App\Models\CreditPack')
                            ->orWhere('orderable_type', 'App\Models\CreditPackPurchase');
                    });

                $promoRevenueQuery = with(clone $ordersCompare)->whereBetween('created_at', [$startOfYear, $endOfYear])
                    ->whereNotNull('promo_code');

                $totalRevenue = round($totalRevenueQuery->sum('value') / 100, 2);
                $totalRevenueCompare->push($totalRevenue);

                $subscriptionRevenue = round($subscriptionRevenueQuery->sum('value') / 100, 2);
                $subscriptionRevenueCompare->push($subscriptionRevenue);

                $creditPackRevenue = round($creditPackRevenueQuery->sum('value') / 100, 2);
                $creditPackRevenueCompare->push($creditPackRevenue);

                $promoRevenue = round($promoRevenueQuery->sum('value') / 100, 2);
                $promoRevenueCompare->push($promoRevenue);

                $avgPerGuest = round($totalRevenue / $members, 2);
                $meanChargeCompare->push($avgPerGuest);
            }
        }

        return response()->json([
            'status' => 'success',
            'chartKeys' => $chartKeys,
            'totalRevenueReport' => $totalRevenueReport,
            'totalRevenueCompare' => $totalRevenueCompare,
            'subscriptionRevenueReport' => $subscriptionRevenueReport,
            'subscriptionRevenueCompare' => $subscriptionRevenueCompare,
            'creditPackRevenueReport' => $creditPackRevenueReport,
            'creditPackRevenueCompare' => $creditPackRevenueCompare,
            'promoRevenueReport' => $promoRevenueReport,
            'promoRevenueCompare' => $promoRevenueCompare,
            'meanChargeReport' => $meanChargeReport,
            'meanChargeCompare' => $meanChargeCompare,
            'purchaseTypeKeys' => $purchaseTypeKeys,
            'purchaseTypeReport' => $purchaseTypeReport,
            'purchaseTypeCompareKeys' => $purchaseTypeCompareKeys,
            'purchaseTypeCompareReport' => $purchaseTypeCompareReport,
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = new \stdClass();
        $stats->today = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\Subscription')->createdToday()->sum('value'), 2);
        $stats->week = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\Subscription')->createdThisWeek()->sum('value'), 2);
        $stats->month = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\Subscription')->createdMonth(date('Y'), date('m'))->sum('value'), 2);
        $stats->year = Helper::CurrencyPenceToPounds(Order::where('orderable_type',
            'App\Models\Subscription')->createdYear(date('Y'))->sum('value'), 2);

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
                    ->where('orderable_type', 'App\Models\Subscription')
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
            ['name' => 'Standard', 'group' => 'standard', 'tiers' => ['standard']],
            [
                'name' => 'Premium',
                'group' => 'premium',
                'tiers' => ['premium', 'premium-membership', 'premium-membership-subscription'],
            ],
            [
                'name' => 'Unlimited',
                'group' => 'unlimited',
                'tiers' => ['unlimited-membership', 'unlimited-membership-subscription'],
            ],
            ['name' => 'VIP', 'group' => 'vip', 'tiers' => ['vip-unlimited']],
        ]);

        foreach ($groups as $group) {
            $stats->push([
                'name' => $group['name'],
                'group' => $group['group'],
                'count' => number_format(Order::select('orders.*')->createdMonth(date('Y'),
                    date('m'))->where('orderable_type', 'App\Models\Subscription')->join('subscriptions',
                    'subscriptions.id', 'orders.orderable_id')->whereIn('subscriptions.tier',
                    $group['tiers'])->count()),
                'sum' => number_format(Order::select('orders.*')->createdMonth(date('Y'),
                        date('m'))->where('orderable_type', 'App\Models\Subscription')->join('subscriptions',
                        'subscriptions.id', 'orders.orderable_id')->whereIn('subscriptions.tier',
                        $group['tiers'])->sum('value') / 100, 2),
            ]);

            $legend->push($group['group']);
            $data->push(number_format(Order::select('orders.*')->createdMonth(date('Y'),
                date('m'))->where('orderable_type', 'App\Models\Subscription')->join('subscriptions',
                'subscriptions.id', 'orders.orderable_id')->whereIn('subscriptions.tier', $group['tiers'])->count()));
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
