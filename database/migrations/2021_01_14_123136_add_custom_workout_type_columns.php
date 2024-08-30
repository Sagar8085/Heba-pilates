<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomWorkoutTypeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('_pivot_workout_exercises', function (Blueprint $table) {
            $table->string('store_workout_type')->nullable();
            $table->integer('custom_duration')->nullable();
            $table->integer('custom_sets')->nullable();
            $table->integer('custom_reps')->nullable();
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
            $table->dropColumn(['store_workout_type', 'custom_duration', 'custom_sets', 'custom_reps']);
        });
    }
}
