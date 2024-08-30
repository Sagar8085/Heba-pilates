<?php

namespace App\Jobs;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserSubscriptionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Get the users latest subscription and update their status based on condidtions.
         */
        $sub = Subscription::where('user_id', $this->user->id)->latest()->withTrashed()->first();

        if ($sub === null) {
            $this->user->update([
                'subscription_status' => null,
            ]);
        } /** Has been deleted. */
        else {
            if ($sub->deleted_at) {
                $this->user->update([
                    'subscription_status' => 'deleted',
                ]);
            } /** Is Active and set to renew. */
            else {
                if ($sub->renew && $sub->expires > date('Y-m-d H:i:s')) {
                    $this->user->update([
                        'subscription_status' => 'active-renews',
                    ]);
                } /** Is Active and not set to renew. */
                else {
                    if (!$sub->renew && $sub->expires > date('Y-m-d H:i:s')) {
                        $this->user->update([
                            'subscription_status' => 'active-does-not-renew',
                        ]);
                    } /** Has Expired. */
                    else {
                        if ($sub->expires < date('Y-m-d H:i:s')) {
                            $this->user->update([
                                'subscription_status' => 'expired',
                            ]);
                        }
                    }
                }
            }
        }
    }
}
