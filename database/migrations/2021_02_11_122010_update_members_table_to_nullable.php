<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMembersTableToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('fitness_goal')->nullable()->change();
            $table->integer('height')->nullable()->change();
            $table->integer('weight')->nullable()->change();
            $table->integer('bmr')->nullable()->change();
            $table->integer('daily_calory_goal')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DO nothing
    }
}
