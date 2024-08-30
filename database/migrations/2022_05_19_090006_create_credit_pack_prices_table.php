<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditPackPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_pack_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('credit_pack_id')
                ->nullable(false);

            $table->string('stripe_price_id')
                ->nullable(false);

            $table->string('name')
                ->nullable(true)
                ->default('Missing data');

            $table->integer('price_in_pence')
                ->nullable(true)
                ->default(999999);

            $table->json('recurring')
                ->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_pack_prices');
    }
}
