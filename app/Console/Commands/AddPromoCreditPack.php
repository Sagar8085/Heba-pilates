<?php

namespace App\Console\Commands;

use App\Models\CreditPack;
use Illuminate\Console\Command;

class AddPromoCreditPack extends Command
{
    protected $signature = 'heba:promo-credit-pack';

    protected $description = 'Adds the promo credit pack for the external endpoint';

    public function handle()
    {
        $creditPack = CreditPack::firstOrCreate([
            'name' => 'Promo 4 sessions',
        ], [
            'description' => 'Promo: 4 sessions (28 day validity)',
            'price' => 0,
            'studio_credits' => 4,
            'days_till_expiry' => 28,
            'stripe_price_id' => 'price_1LONCsKlaf0ZXKqlTsTqxMMK',
            'stripe_product_id' => 'prod_M6aIHmOduTmGYU',
        ]);

        $this->info($creditPack->name . ' updated successfully');
    }
}
