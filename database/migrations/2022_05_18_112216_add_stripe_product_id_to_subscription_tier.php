<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeProductIdToSubscriptionTier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->string('stripe_product_id')
                ->after('product_id')
                ->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->dropColumn('stripe_product_id');
        });
    }
}
