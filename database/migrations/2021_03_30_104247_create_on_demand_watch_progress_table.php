<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnDemandWatchProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_demand_watch_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('ondemand_id')->index();
            $table->integer('user_id')->index();
            $table->integer('time');
            $table->tinyInteger('completed')->index();
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
        Schema::dropIfExists('on_demand_watch_progress');
    }
}
