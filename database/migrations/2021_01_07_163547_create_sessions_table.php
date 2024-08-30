<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->index();
            $table->integer('trainer_id')->index();
            $table->integer('user_package_id')->index()->nullable();
            $table->timestamp('datetime')->index();
            $table->string('date')->index();
            $table->string('start')->index();
            $table->integer('length');
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->double('price', 8, 2)->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
