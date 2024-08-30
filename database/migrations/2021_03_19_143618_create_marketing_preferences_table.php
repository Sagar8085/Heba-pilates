<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_preferences', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->index();
            $table->tinyInteger('account')->default(1);
            $table->tinyInteger('new_content')->default(1);
            $table->tinyInteger('bookings')->default(1);
            $table->tinyInteger('marketing')->default(1);
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
        Schema::dropIfExists('marketing_preferences');
    }
}
