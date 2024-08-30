<?php

use App\Models\SubscriptionTier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpirySettingToSubscriptionTier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->integer('months_till_expiry')->default(1)->after('admin_only');
        });

        SubscriptionTier::where('slug', 'vip-unlimited-2')->update(['months_till_expiry' => 12]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_tiers', function (Blueprint $table) {
            $table->dropColumn('months_till_expiry');
        });
    }
}
