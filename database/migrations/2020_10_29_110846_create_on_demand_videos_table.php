<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnDemandVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_demand_videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->double('price', 8, 2)->default(0);
            $table->string('video_path')->nullable();
            $table->string('image_path')->nullable();
            $table->tinyInteger('processed')->default(0);
            $table->integer('instructor_id')->index();
            $table->integer('created_by');
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
        Schema::dropIfExists('on_demand_videos');
    }
}
