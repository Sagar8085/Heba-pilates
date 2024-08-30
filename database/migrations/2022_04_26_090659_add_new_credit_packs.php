<?php

use App\Models\CreditPack;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewCreditPacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->runningUnitTests()) {
            // When running tests we don't want to create this data.
            return;
        }

        CreditPack::forceCreate([
            'id' => 15,
            'name' => 'No Show',
            'description' => 'Member did not show up to their session.',
            'price' => 500,
            'promotional' => 0,
            'online_credits' => 0,
            'studio_credits' => 0,
            'days_till_expiry' => null,
            'months_till_expiry' => null,
            'stripe_price_id' => '', // This will need to be set manually.
            'stripe_product_id' => null,
            'apple_product_id' => null,
            'google_product_id' => null,
        ]);

        CreditPack::forceCreate([
            'id' => 16,
            'name' => 'Gifted Credit',
            'description' => 'Gift credits to a member',
            'price' => 0,
            'promotional' => 0,
            'online_credits' => 1,
            'studio_credits' => 1,
            'days_till_expiry' => null,
            'months_till_expiry' => null,
            'stripe_price_id' => null,
            'stripe_product_id' => null,
            'apple_product_id' => null,
            'google_product_id' => null,
        ]);

        CreditPack::forceCreate([
            'id' => 17,
            'name' => 'Staff Unlimited',
            'description' => 'Staff Unlimited Credits',
            'price' => 0,
            'promotional' => 0,
            'online_credits' => 10000,
            'studio_credits' => 10000,
            'days_till_expiry' => null,
            'months_till_expiry' => 12,
            'stripe_price_id' => null,
            'stripe_product_id' => null,
            'apple_product_id' => null,
            'google_product_id' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        CreditPack::whereIn('id', [15, 16, 17])->delete();
    }
}
