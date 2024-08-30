<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotCreditPackPriceGymTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_pivot_credit_pack_price_gym', function (Blueprint $table) {
            $table->id();

            $table->foreignId('credit_pack_price_id')
                ->nullable(false);

            $table->foreignId('gym_id')
                ->nullable(false);

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
        Schema::dropIfExists('_pivot_credit_pack_price_gym');
    }
}
