<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_calls', function (Blueprint $table) {
            $table->id();
            $table->integer('agent_id');
            $table->integer('lead_id');
            $table->timestamp('datetime');
            $table->string('outcome');
            $table->integer('note_id');
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
        Schema::dropIfExists('leads_calls');
    }
}
