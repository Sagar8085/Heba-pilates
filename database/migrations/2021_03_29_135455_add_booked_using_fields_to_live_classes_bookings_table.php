<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookedUsingFieldsToLiveClassesBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_classes_bookings', function (Blueprint $table) {
            $table->string('booked_using_type')->after('member_id')->nullable();
            $table->integer('booked_using_id')->after('booked_using_type')->nullable();
            $table->string('location')->after('booked_using_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_classes_bookings', function (Blueprint $table) {
            $table->dropColumns(['booked_using_id', 'booked_using_type', 'location']);
        });
    }
}
