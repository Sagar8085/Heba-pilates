<?php

use App\Models\SubscriptionTier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewSubscription extends Migration
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

        SubscriptionTier::forceCreate([
            'name' => 'Heba Ambassador Monthly',
            'slug' => 'ambassador-monthly',
            'price' => 10000,
            'product_group' => '',
            'product_id' => '',
            'stripe_price_id' => '', // This will need to be set manually.
            'online_credits' => 10000,
            'studio_credits' => 10000,
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

        SubscriptionTier::where('slug', 'ambassador-monthly')->delete();
    }
}
