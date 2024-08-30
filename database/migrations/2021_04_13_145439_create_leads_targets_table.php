<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('agent_id');
            $table->integer('calls');
            $table->integer('appointments');
            $table->integer('signups');
            $table->integer('close_ratio');
            $table->integer('leads');
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
        Schema::dropIfExists('leads_targets');
    }
}
