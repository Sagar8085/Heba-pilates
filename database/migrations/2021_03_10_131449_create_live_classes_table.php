<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->index();
            $table->integer('instructor_id')->index();
            $table->integer('duration')->index();
            $table->timestamp('datetime')->index();
            $table->string('room_token')->index()->nullable();
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
        Schema::dropIfExists('live_classes');
    }
}
