<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeadAboutUsToMarketingPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_preferences', function (Blueprint $table) {
            $table->string('heard_about_us')->nullable()->after('marketing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketing_preferences', function (Blueprint $table) {
            $table->dropColumn('heard_about_us');
        });
    }
}
