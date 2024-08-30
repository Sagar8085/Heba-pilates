<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses_episodes', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id')->index();
            $table->string('name');
            $table->text('description');
            $table->integer('episode_number');
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
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
        Schema::dropIfExists('courses_episodes');
    }
}
