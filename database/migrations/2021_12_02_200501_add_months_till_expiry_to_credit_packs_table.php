<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonthsTillExpiryToCreditPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_packs', function (Blueprint $table) {
            $table->integer('months_till_expiry')->nullable()->after('days_till_expiry');
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
            $table->dropColumn('months_till_expiry');
        });
    }
}
