<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotExerciseExerciseStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_pivot_exercise_exercise_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('exercise_id');
            $table->unsignedInteger('exercise_step_id');
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
        Schema::dropIfExists('_pivot_exercise_exercise_steps');
    }
}
