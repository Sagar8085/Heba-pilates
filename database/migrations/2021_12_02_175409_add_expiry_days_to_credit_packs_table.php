<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiryDaysToCreditPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_packs', function (Blueprint $table) {
            $table->integer('days_till_expiry')->nullable()->after('studio_credits');
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
            $table->dropColumn('days_till_expiry');
        });
    }
}
