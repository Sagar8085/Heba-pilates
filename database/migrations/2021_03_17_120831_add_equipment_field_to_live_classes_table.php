<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEquipmentFieldToLiveClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->text('description')->after('datetime')->nullable();
            $table->string('equipment')->after('description')->nullable();
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
            $table->dropColumns(['equipment', 'description']);
        });
    }
}
