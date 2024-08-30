<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotGymSubscriptionTierPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_pivot_gym_subscription_tier_price', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_id')
                ->nullable(false);

            $table->foreignId('subscription_tier_price_id')
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
        Schema::dropIfExists('_pivot_gym_subscription_tier_price');
    }
}
