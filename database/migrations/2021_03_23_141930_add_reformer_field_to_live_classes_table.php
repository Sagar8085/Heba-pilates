<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReformerFieldToLiveClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_classes', function (Blueprint $table) {
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
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumns(['reformer']);
        });
    }
}
