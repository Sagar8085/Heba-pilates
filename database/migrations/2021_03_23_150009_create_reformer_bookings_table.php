<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReformerBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reformer_bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->nullable();
            $table->integer('reformer_id')->index()->nullable();
            $table->timestamp('datetime')->index()->nullable();
            $table->integer('bookable_id')->index()->nullable();
            $table->string('bookable_type')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reformer_bookings');
    }
}
