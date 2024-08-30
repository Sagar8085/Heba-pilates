<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditPacksPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_packs_purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('credit_pack_id')->index();
            $table->integer('order_id')->index();
            $table->integer('online_credits')->default(0);
            $table->integer('studio_credits')->default(0);
            $table->timestamp('expires')->nullable();
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
        Schema::dropIfExists('credit_packs_purchases');
    }
}
