<?php

namespace App\Console\Commands;

use App\Models\SubscriptionTier;
use App\Models\SubscriptionTierPrice;
use App\Services\Stripe;
use Illuminate\Console\Command;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;

class UpdateSubscriptionTierPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:subscription-tier-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the subscription_tier_prices table with price data associated with subscription_tier stripe_product_id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Stripe $stripe)
    {
        SubscriptionTier::whereNotNull('stripe_product_id')
            ->get()
            ->each(function (SubscriptionTier $subscriptionTier) use ($stripe): void {
                try {
                    collect(
                        $stripe->getPricesForProduct($subscriptionTier->stripe_product_id)->getIterator(),
                    )
                        ->reject(
                            fn (Price $price
                            ): bool => $subscriptionTier->subscription_tier_prices->contains('stripe_price_id',
                                $price->id),
                        )
                        ->each(function (Price $price) use ($subscriptionTier): void {
                            /** @var SubscriptionTierPrice $subscriptionTierPrice */
                            $subscriptionTierPrice = $subscriptionTier->subscription_tier_prices()->create([
                                'stripe_price_id' => $price->id,
                                'name' => $price->nickname,
                                'price_in_pence' => $price->unit_amount,
                            ]);

                            $subscriptionTierPrice->recurring = $price->recurring?->toArray();
                            $subscriptionTierPrice->save();
                        });
                } catch (ApiErrorException $_) {
                    // Do nothing.
                }
            });

        echo "Subscription tier prices populated\n";

        return 0;
    }
}
