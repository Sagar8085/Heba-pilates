<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Membership\UpdateRequest;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriptionTierResource;
use App\Models\Gym;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function all(Request $request)
    {
        $memberships = Subscription::select('subscriptions.*')->withTrashed()->latest()->with('member', 'order');

        if (!$request->paymentDateAll) {
            $memberships = $memberships->where('created_at', '>=',
                $request->startDate . ' 00:00:00')->where('created_at', '<=', $request->endDate . ' 23:59:59');
        }

        if (!$request->expiryDateAll) {
            $memberships = $memberships->where('expires', '>=',
                $request->expiryStartDate . ' 00:00:00')->where('expires', '<=', $request->expiryEndDate . ' 23:59:59');
        }


        if ($request->selectedMembershipTiers) {
            $ids = collect($request->selectedMembershipTiers)->pluck('slug')->toArray();
            $memberships = $memberships->whereIn('tier', $ids);
        }

        if ($request->download === 'true') {
            $memberships = $memberships->get();

            // Create CSV in Memory
            $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());

            // Add Headers
            $csv->insertOne([
                'Tier',
                'Status',
                'Member Name',
                'Member Email',
                'Member Tel',
                'Expiry Date',
                'Purchase Date',
            ]);

            // Add Results
            foreach ($memberships as $membership) {
                $csv->insertOne([
                    $membership->name,
                    $membership->status_human,
                    $membership->member->name,
                    $membership->member->email,
                    $membership->member->phone_number,
                    $membership->expires,
                    $membership->created_at,
                ]);
            }

            $csv->output('export.csv');
            die;
        }

        $memberships = $memberships->paginate(25);

        return response()->json($memberships);
    }

    public function single(Subscription $membership)
    {
        $membership->member = $membership->member;
        return response()->json($membership);
    }

    public function membershipStats()
    {
        $stats = new \stdClass();
        $stats->active = Subscription::where('expires', '>', Carbon::now())->count();
        $stats->expiring = Subscription::where('expires', '>=',
            Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')->where('expires', '<=',
            Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59')->where('renew', 0)->count();
        $stats->renewing = Subscription::where('expires', '>=',
            Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')->where('expires', '<=',
            Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59')->where('renew', 1)->count();
        $stats->revenue = number_format((Order::where('created_at', '>=',
                Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00')->where('created_at', '<=',
                Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59')->where('orderable_type',
                'App\Models\Subscription')->sum('value') / 100), 2);

        return response()->json($stats);
    }

    /**
     * Load all membership tiers.
     *
     * @param None
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tiers()
    {
        return response()->json(
            SubscriptionTierResource::collection(SubscriptionTier::all())
        );
    }

    /**
     * @param Gym $gym
     *
     * @return JsonResponse
     */
    public function allTiersForGym(Gym $gym): JsonResponse
    {
        return response()->json(
            SubscriptionTier::byGym($gym)->get(),
        );
    }

    public function update(Subscription $subscription, UpdateRequest $request)
    {
        $subscription->fill($request->validated())->save();
        $subscription->load('member');

        return SubscriptionResource::make($subscription)->additional([
            'status' => __('response.success'),
            'message' => __('membership.updated'),
        ]);
    }
}
