<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReformerFieldToOnDemandVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('on_demand_videos', function (Blueprint $table) {
            $table->tinyInteger('reformer')->after('duration')->default(0);
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
            $table->dropColumns(['reformer']);
        });
    }
}
