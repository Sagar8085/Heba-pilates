<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;


class MembersLostAfterIntro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:lost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get members who bought an intro pack and completed the 3 sessions but not bought anything else moving forward';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $members = User::where('role_id', 4)
        //     ->whereHas('creditPackPurchases', function($query) {
        //         $query->where('credit_pack_id', 1);
        //         $query->where('studio_credits', 0);
        //     }, '=', 1)
        //     ->whereDoesntHave('creditPackPurchases', function($query) {
        //         $query->where('credit_pack_id', '!=', 1);
        //     })
        //     ->doesntHave('subscriptions')
        //     ->get();

        // $members = User::with([
        //         'creditPackPurchases' => function($query) {
        //             $query->selectRaw('*, SUM(studio_credits) as credit_pack_credits');
        //         },
        //         'subscriptions' => function($query) {
        //             $query->selectRaw('*, SUM(subscriptions.studio_credits) as subscription_credits');
        //         }
        //     ])
        // ->whereHas('creditPackPurchases', function($query) {
        //     $query->selectRaw('sum(studio_credits) as total_credits')->groupBy('user_id')
        //         ->having('total_credits', 12);
        // })
        // ->where('id', 1);

        // Pro query
        // $creditPacks = DB::table('credit_packs_purchases')
        //     ->select('user_id', DB::raw('SUM(credit_packs_purchases.studio_credits) as credit_pack_credits'))
        //     ->groupBy('user_id');
        //
        // $subscriptions = DB::table('subscriptions')
        //     ->select('user_id', DB::raw('SUM(subscriptions.studio_credits) as subscription_credits'))
        //     ->groupBy('user_id');
        //
        // $members = User::selectRaw('users.*, credit_pack_credits, subscription_credits, SUM(credit_pack_credits+subscription_credits) as total_credits')
        //     ->leftJoinSub($creditPacks, 'credit_packs', function($join) {
        //         $join->on('users.id', 'credit_packs.user_id');
        //     })
        //     ->leftJoinSub($subscriptions, 'subscriptions', function($join) {
        //         $join->on('users.id', 'subscriptions.user_id');
        //     })
        //     ->having('total_credits', 7)
        //     ->groupBy('users.id')
        //     ->get();


        // Average visits per week
        // $members = User::whereHas('reformerBookings', function($query) {
        //         $query->selectRaw('COUNT(id)/4 as avg_visits_per_week')->having('avg_visits_per_week', 3.25)
        //             ->whereBetween('datetime', [Carbon::now()->subDays(28), Carbon::now()]);
        //     })
        //     ->get();

        // $members = User::selectRaw('users.*, SUM(credit_packs_purchases.studio_credits) as credit_pack_credits, SUM(subscriptions.studio_credits) as subscription_credits')
        //     ->leftJoin('credit_packs_purchases', 'credit_packs_purchases.user_id', 'users.id')
        //     ->leftJoin('subscriptions', 'subscriptions.user_id', 'users.id')
        //     ->groupBy('users.id')
        //     ->where('users.id', 1)
        //     ->get();

        $members = User::with('lead.leadCalls')
            ->whereHas('lead.leadCalls', function ($query) {
                $query->whereBetween('datetime', ['2022-01-18 00:00:00', '2022-01-18 23:59:59']);
            })
            ->whereDoesntHave('lead.leadCalls', function ($query) {
                $query->whereBetween('datetime', ['2022-01-18 23:59:59', Carbon::now()]);
            })
            ->get();

        // $members = $members->whereHas('recentReformerBooking', function($query) use($request) {
        //     $query->where('datetime', '>=', $request->lastVisitStartDate . ' 00:00:00')
        //         ->where('datetime', '<=', $request->lastVisitEndDate . ' 23:59:59');
        // })
        // ->whereDoesntHave('reformerBookings', function($query) use($request) {
        //     $query->whereBetween('datetime', [Carbon::now(), Carbon::parse($request->lastVisitEndDate)]);
        // });

        Log::debug($members);

        return 0;
    }
}
