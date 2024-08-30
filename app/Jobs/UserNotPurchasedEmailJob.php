<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class UserNotPurchasedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::where('role_id', 4)
            ->whereBetween('created_at', [Carbon::now()->subHours(25), Carbon::now()->subHours(24)])
            ->doesntHave('creditPackPurchases')
            ->get();

        Log::debug('--- SCHEDULED JOB ---');
        Log::debug($users);
        Log::debug('------');

        foreach ($users as $user) {
            SendEmailJob::dispatch($user, 'emails.user.twentyfour-purchase-email',
                'Heba Pilates Intro Pack Offer - Don’t miss out', ['user' => $user])->onQueue('account-notifications');
        }
    }
}
