<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParqResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parq_responses', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('current_injuries');
            $table->tinyInteger('taking_medication');
            $table->tinyInteger('advised_by_doctor');
            $table->string('advised_by_doctor_details');
            $table->tinyInteger('currently_pregnant');
            $table->string('contact_first_name');
            $table->string('contact_last_name');
            $table->string('contact_phone_number');
            $table->string('contact_email');
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
        Schema::dropIfExists('parq_responses');
    }
}
