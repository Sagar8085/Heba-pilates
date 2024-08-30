<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeProductIdToCreditPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_packs', function (Blueprint $table) {
            $table->string('stripe_product_id')->after('stripe_price_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_packs', function (Blueprint $table) {
            $table->dropColumn('stripe_product_id');
        });
    }
}
