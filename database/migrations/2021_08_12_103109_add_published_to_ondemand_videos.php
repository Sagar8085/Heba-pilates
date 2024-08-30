<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishedToOndemandVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('on_demand_videos', function (Blueprint $table) {
            $table->boolean('published')->default('1')->after('processed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('on_demand_videos', function (Blueprint $table) {
            $table->dropColumns(['published']);
        });
    }
}
