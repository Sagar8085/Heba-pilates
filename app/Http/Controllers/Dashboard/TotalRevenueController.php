<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\Leads\Pie\PromotionConversions;
use App\Charts\Orders\Bar\PendingSubscriptionRevenue;
use App\Charts\Orders\Line\AverageYield;
use App\Charts\Orders\Line\CreditPackChurn;
use App\Charts\Orders\Line\CreditPackNew;
use App\Charts\Orders\Line\CreditPackRenewal;
use App\Charts\Orders\Line\StandardMonthlySubscribers;
use App\Charts\Orders\Line\SubscriptionChurn;
use App\Charts\Orders\Line\SubscriptionNew;
use App\Charts\Orders\Line\SubscriptionRenewal;
use App\Charts\Orders\Line\TotalCreditPackRevenue;
use App\Charts\Orders\Line\TotalPromoRevenue;
use App\Charts\Orders\Line\TotalSalesRevenue;
use App\Charts\Orders\Line\TotalSubscriptionRevenue;
use App\Charts\Orders\Pie\PurchaseTypeBreakdown;
use App\Charts\Orders\Pie\VolumeOfCreditPackSales;
use App\Charts\Orders\Pie\VolumeOfSubscriptionSales;
use App\Http\Controllers\Controller;
use App\Http\Requests\RevenueIndexRequest;
use App\Models\Lead;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class TotalRevenueController extends Controller
{
    public function __invoke(RevenueIndexRequest $request): JsonResponse
    {
        $unit = $request->input('chartUnit');
        $report_date_from = $request->input('report_date_from') ? Carbon::parse($request->input('report_date_from'))->startOfDay() : null;
        $report_date_to = $request->input('report_date_to') ? Carbon::parse($request->input('report_date_to'))->endOfDay() : null;
        $compare_date_from = $request->input('compare_date_from') ? Carbon::parse($request->input('compare_date_from'))->startOfDay() : null;
        $compare_date_to = $request->input('compare_date_to') ? Carbon::parse($request->input('compare_date_to'))->endOfDay() : null;

        $orders = Order::query()
            ->when(
                $request->input('locations'),
                function (Builder $query, array $locations) {
                    return $query->whereHas('member.memberProfile', function ($query) use ($locations) {
                        return $query->whereIn('home_studio_id', $locations);
                    });
                }
            );

        $reportOrders = with(clone $orders)
            ->when(
                $report_date_from,
                fn (Builder $query, Carbon $from) => $query->where('created_at', '>=', $from->toDateTimeString())
            )
            ->when(
                $report_date_to,
                fn (Builder $query, Carbon $to) => $query->where('created_at', '<=', $to->toDateTimeString())
            );

        $comparisonOrders = with(clone $orders)
            ->when(
                $request->input('locations'),
                function (Builder $query, array $locations) {
                    return $query->whereHas('member.memberProfile', function ($query) use ($locations) {
                        return $query->whereIn('home_studio_id', $locations);
                    });
                }
            )
            ->when(
                $compare_date_from,
                fn (Builder $query, $from) => $query->whereDate('created_at', '>=', $from)
            )
            ->when(
                $compare_date_to,
                fn (Builder $query, $to) => $query->whereDate('created_at', '<=', $to)
            );

        $reportLeads = Lead::query()
            ->when(
                $report_date_from,
                fn (Builder $query, Carbon $from) => $query->where('created_at', '>=', $from->toDateTimeString())
            )
            ->when(
                $report_date_to,
                fn (Builder $query, Carbon $to) => $query->where('created_at', '<=', $to->toDateTimeString())
            );

        $comparisonLeads = Lead::query()
            ->when(
                $compare_date_from,
                fn (Builder $query, Carbon $from) => $query->where('created_at', '>=', $from->toDateTimeString())
            )
            ->when(
                $compare_date_to,
                fn (Builder $query, Carbon $to) => $query->where('created_at', '<=', $to->toDateTimeString())
            );

        $subscriptionFilteredReportOrders = with(clone $reportOrders)->onlyCertainSubscriptionTypes($request->input('subscriptionTypes',
            []))->get();
        $subscriptionFilteredComparisonOrders = with(clone $comparisonOrders)->onlyCertainSubscriptionTypes($request->input('subscriptionTypes',
            []))->get();

        $creditPackFilteredReportOrders = with(clone $reportOrders)
            ->when(
                $request->input('creditPackTypes', []),
                function (Builder $query, $types) {
                    $query->whereIn('id', $types);
                }
            );

        $creditPackFilteredComparisonOrders = with(clone $comparisonOrders)
            ->when(
                $request->input('creditPackTypes', []),
                function (Builder $query, $types) {
                    $query->whereIn('id', $types);
                }
            );

        return response()->json([
            'totalRevenue' => (new TotalSalesRevenue($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'averageYield' => (new AverageYield($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'purchaseTypeBreakdown' => (new PurchaseTypeBreakdown($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'allSubscriptions' => (new TotalSubscriptionRevenue($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'allCreditPacks' => (new TotalCreditPackRevenue($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'allPromos' => (new TotalPromoRevenue($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'subscriptionRenewals' => (new SubscriptionRenewal($subscriptionFilteredReportOrders,
                $subscriptionFilteredComparisonOrders))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'subscriptionNew' => (new SubscriptionNew($subscriptionFilteredReportOrders,
                $subscriptionFilteredComparisonOrders))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'subscriptionChurn' => (new SubscriptionChurn($subscriptionFilteredReportOrders,
                $subscriptionFilteredComparisonOrders))
                ->setUnit($unit)
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'standardMonthlySubscribers' => (new StandardMonthlySubscribers($reportOrders->get(),
                $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'volumeOfSubscriptionSales' => (new VolumeOfSubscriptionSales($reportOrders->get(),
                $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'pendingSubscriptionRevenue' => (new PendingSubscriptionRevenue($reportOrders->get(),
                $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'creditPackRenewals' => (new CreditPackRenewal($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'creditPackNew' => (new CreditPackNew($creditPackFilteredReportOrders->get(),
                $creditPackFilteredComparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'creditPackChurn' => (new CreditPackChurn($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'volumeOfCreditPackSales' => (new VolumeOfCreditPackSales($reportOrders->get(), $comparisonOrders->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
            'promoConversions' => (new PromotionConversions($reportLeads->get(), $comparisonLeads->get()))
                ->setUnit($unit)
                ->setFrom($report_date_from)
                ->setTo($report_date_to)
                ->setCompareFrom($compare_date_from)
                ->setCompareTo($compare_date_to)
                ->toChart(),
        ]);
    }
}
