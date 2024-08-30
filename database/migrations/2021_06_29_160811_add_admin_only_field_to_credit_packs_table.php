<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminOnlyFieldToCreditPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_packs', function (Blueprint $table) {
            $table->tinyInteger('admin_only')->default(0)->after('google_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_packs', function (Blueprint $table) {
            $table->dropColumns(['admin_only']);
        });
    }
}
