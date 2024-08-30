<?php

namespace App\Console\Commands;

use App\Models\CreditPack;
use App\Models\SubscriptionTierPrice;
use App\Services\Stripe;
use Illuminate\Console\Command;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;

class UpdateCreditPackPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:credit-pack-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the credit_pack_prices table with price data associated with credit_pack stripe_product_id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Stripe $stripe)
    {
        CreditPack::whereNotNull('stripe_product_id')
            ->get()
            ->each(function (CreditPack $creditPack) use ($stripe): void {
                try {
                    collect(
                        $stripe->getPricesForProduct($creditPack->stripe_product_id)->getIterator(),
                    )
                        ->reject(
                            fn (Price $price): bool => $creditPack->credit_pack_prices->contains('stripe_price_id',
                                $price->id),
                        )
                        ->each(function (Price $price) use ($creditPack): void {
                            /** @var SubscriptionTierPrice $subscriptionTierPrice */
                            $subscriptionTierPrice = $creditPack->credit_pack_prices()->create([
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

        echo "Credit pack prices populated\n";

        return 0;
    }
}
