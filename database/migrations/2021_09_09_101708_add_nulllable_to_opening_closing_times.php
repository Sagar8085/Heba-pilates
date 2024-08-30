<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNulllableToOpeningClosingTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opening_times', function (Blueprint $table) {
            $table->string('opening_time')->nullable()->change();
            $table->string('closing_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opening_times', function (Blueprint $table) {
            //
        });
    }
}
