<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_appointments', function (Blueprint $table) {
            $table->id();

            $table->integer('agent_id');
            $table->integer('lead_id');

            $table->timestamp('datetime');
            $table->integer('duration');
            $table->string('outcome');
            $table->string('outcome_reason');

            $table->integer('note_id');
            $table->integer('gym_id');

            $table->boolean('subscribe_weekly')->default(false);
            $table->boolean('subscribe_monthly')->default(false);
            $table->boolean('unsubscribe')->default(false);
            

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
        Schema::dropIfExists('leads_appointments');
    }
}
