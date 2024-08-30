<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomWorkoutTypeColumns2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('_pivot_workout_exercises', function (Blueprint $table) {
            $table->integer('custom_rest')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('_pivot_workout_exercises', function (Blueprint $table) {
            $table->dropColumn(['custom_rest']);
        });
    }
}
