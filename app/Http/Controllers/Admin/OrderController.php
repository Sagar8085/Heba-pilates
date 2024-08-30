<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Fetch listing of all orders.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \League\Csv\CannotInsertRecord
     */
    public function all(Request $request)
    {
        $orders = CreditPackPurchase::with('pack', 'member', 'order')->latest();

        if (!$request->paymentDateAll) {
            $orders = $orders->where('created_at', '>=', $request->startDate . ' 00:00:00')->where('created_at', '<=',
                $request->endDate . ' 23:59:59');
        }

        if ($request->selectedCreditPacks) {
            $ids = collect($request->selectedCreditPacks)->pluck('id')->toArray();
            $orders = $orders->whereIn('id', $ids);
        }

        if (request('download') === 'true') {
            $orders = $orders->get();

            // Create CSV in Memory
            $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());

            // Add Headers
            $csv->insertOne([
                'Purchase',
                'Member',
                'Date',
            ]);

            // Add Results
            foreach ($orders as $order) {
                $csv->insertOne([
                    $order->pack->name,
                    $order->member->name,
                    $order->created_human,
                ]);
            }

            $csv->output('export.csv');
            die;
        }

        $orders = $orders->paginate(25);

        return response()->json($orders);
    }

    /**
     * Load a single order resource.
     *
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Order $order)
    {
        $order->member = $order->member;
        $order->trainer = $order->trainer;
        return response()->json($order);
    }

    /**
     * Load all credit packs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function creditPacks()
    {
        $creditPacks = CreditPack::get();
        return response()->json($creditPacks);
    }

    public function creditPackStats()
    {
        $stats = new \stdClass();
        $stats->purchased = Order::createdMonth(date('Y'), date('m'))->count();
        $stats->revenue = number_format((Order::createdMonth(date('Y'), date('m'))->sum('value') / 100));

        return response()->json($stats);
    }

    public function singleCreditPack(CreditPackPurchase $purchase)
    {
        $purchase->pack = $purchase->pack;
        $purchase->events = $purchase->events;
        return $purchase;
    }

    public function updateExpiry(CreditPackPurchase $purchase, Request $request)
    {
        Event::create([
            'message' => 'Expiry updated from ' . Carbon::parse($purchase->expires)->format('Y-m-d') . ' to ' . $request->expiry,
            'user_id' => auth()->user()->id,
            'object_id' => $purchase->id,
            'object_type' => 'App\Models\CreditPackPurchase',
            'created_by' => auth()->user()->id,
        ]);

        $purchase->update([
            'expires' => $request->expiry . ' 23:59:59',
        ]);
    }
}
