<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminOnlyFieldToMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->tinyInteger('admin_only')->default(0)->after('studio_credits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->dropColumns(['admin_only']);
        });
    }
}
