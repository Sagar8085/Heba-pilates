<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIDFieldToOnDemandVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('on_demand_videos', function (Blueprint $table) {
            $table->integer('category_id')->after('reformer')->index()->default(0);
            $table->integer('order')->after('category_id')->default(0);
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
            $table->dropColumns(['category_id', 'order']);
        });
    }
}
