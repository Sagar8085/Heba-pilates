<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotWorkoutExerciseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_pivot_workout_exercises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('workout_id');
            $table->unsignedInteger('exercise_id');
            $table->unsignedInteger('workout_section_id');
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
        Schema::dropIfExists('_pivot_workout_exercises');
    }
}
