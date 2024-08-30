<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotOnDemandCategoryGymsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_pivot_on_demand_category_gyms', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->index();
            $table->integer('gym_id')->index();
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
        Schema::dropIfExists('_pivot_on_demand_category_gyms');
    }
}
