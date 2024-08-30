<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(Subscription::ALL)->each(fn ($tier) =>
            SubscriptionTier::factory([
                'name' => $tier,
                'slug' => $tier,
            ])->createQuietly()
        );
    }
}
