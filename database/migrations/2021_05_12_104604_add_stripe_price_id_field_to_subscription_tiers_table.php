<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripePriceIdFieldToSubscriptionTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->index()->after('product_id');
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
            $table->dropColumns(['stripe_price_id']);
        });
    }
}
