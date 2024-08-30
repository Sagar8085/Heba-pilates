<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpeningTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opening_times', function (Blueprint $table) {
            $table->id();
            $table->integer('gym_id')->index();
            $table->tinyInteger('closed')->default(0);
            $table->string('date');
            $table->string('opening_time');
            $table->string('closing_time');
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
        Schema::dropIfExists('opening_times');
    }
}
