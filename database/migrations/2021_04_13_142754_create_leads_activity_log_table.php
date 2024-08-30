<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_activity_log', function (Blueprint $table) {
            $table->id();

            $table->integer('agent_id');
            $table->integer('lead_id');

            $table->integer('activity_type');
            $table->integer('note_id');

            $table->string('details');
            $table->string('extra_details');
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
        Schema::dropIfExists('leads_activity_log');
    }
}
