<?php

namespace App\Console\Commands;

use App\Models\CreditPackPurchase;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateCreditPackExpiryDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:creditpackexpiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the expiry dates of credit packs from their original purchase dates.';

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
        $purchases = CreditPackPurchase::get();

        foreach ($purchases as $purchase) {
            $purchase->update([
                'expires' => $purchase->pack->days_till_expiry !== null ? Carbon::parse($purchase->created_at)->addDays($purchase->pack->days_till_expiry) : ($purchase->pack->months_till_expiry !== null ? Carbon::parse($purchase->created_at)->addMonths($purchase->pack->months_till_expiry) : null),
            ]);
        }
    }
}
