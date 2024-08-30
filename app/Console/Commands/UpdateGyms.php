<?php

namespace App\Console\Commands;

use App\Models\CreditPack;
use App\Models\CreditPackPrice;
use App\Models\Gym;
use App\Models\SubscriptionTier;
use App\Models\SubscriptionTierPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UpdateGyms extends Command
{
    protected $signature = 'heba:gyms';

    protected $description = 'A command to add the new gym to the platform';

    private array $liveModeProductIDs = [
        'Unlimited Membership' => 'prod_KVmFGyjk3laoru',
        'Twice Weekly' => 'prod_L6VjoM2CG73W8x',
        'Once Weekly' => 'prod_L6VjTW5OVd27Cj',
        'Unlimited Annual' => 'prod_JtSp4prJvnRENr',
        'Intro Pack' => 'prod_K1KjQ47AeHzTS6',
        'Single Session' => 'prod_K1KjgXgzWUT6du',
        '30 Sessions' => 'prod_K1KkzUFMD9l73l',
        '10 Sessions' => 'prod_K1KkzUFMD9l73l',
    ];

    private array $testModeProductIDs = [
        'Unlimited Membership' => 'prod_LiU5RYMaM8ZN6b',
        'Twice Weekly' => 'prod_LiU5fkeaG0C3Md',
        'Once Weekly' => 'prod_LiU54rfqlTZj4a',
        'Unlimited Annual' => 'prod_JtSp4prJvnRENr',
        'Intro Pack' => 'prod_LiU5KBMBzgQGyX',
        'Single Session' => 'prod_LiU5mS0v8U722a',
        '30 Sessions' => 'prod_LiU5CT6uYer6Fr',
        '10 Sessions' => 'prod_LiU57xvBs3AU5o',
    ];

    private array $liveModeNormalPrices = [
        'subscriptions' => [
            'price_1KQIhZKlaf0ZXKqlYQGbR4Jg',
            'price_1KQIiLKlaf0ZXKqlEpkcxm4K',
            'price_1KQKYLKlaf0ZXKqlDO3qHqw2',
            'price_1KQKk5Klaf0ZXKqlMr7mClqF',
        ],
        'packs' => [
            'price_1JNIBZKlaf0ZXKql08co8bDv',
            'price_1JNI4WKlaf0ZXKql5zG9v1YX',
            'price_1KQKj5Klaf0ZXKqliTYKvNPs',
            'price_1KQKinKlaf0ZXKqlyNy78iV2',
        ],
    ];

    private array $liveModeLondonPrices = [
        'subscriptions' => [
            'price_1L1FxHKlaf0ZXKqleHj7vkdx',
            'price_1L1FwNKlaf0ZXKqlTg9Z0Zm4',
            'price_1L1FplKlaf0ZXKqlw1WAJMSM',
            'price_1L1G74Klaf0ZXKqlJjKsMIcF',
            'price_1L1G8gKlaf0ZXKql29Z7toXB',
            'price_1L1G01Klaf0ZXKql8qYAShsA',
        ],
        'packs' => [
            'price_1L1G3KKlaf0ZXKqlHXcBwMtt',
            'price_1JNI4WKlaf0ZXKql5zG9v1YX',
            'price_1L1G2IKlaf0ZXKqlMtQg03hx',
            'price_1L1G1aKlaf0ZXKqlj7dkCezT',
        ],
    ];

    private array $testModeNormalPrices = [
        'subscriptions' => [
            'price_1L13W2Klaf0ZXKql3XY2q1Tt',
            'price_1L13NIKlaf0ZXKqlHNuYaN5Z',
            'price_1L13OkKlaf0ZXKqlCu5QcKYe',
        ],
        'packs' => [
            'price_1L13W8Klaf0ZXKqlfvpG9wd4',
            'price_1L13W6Klaf0ZXKqlGw61l9sQ',
            'price_1L13W5Klaf0ZXKqljCQKpb4p',
            'price_1L13W4Klaf0ZXKql5ycg0Ala',
        ],
    ];

    private array $testModeLondonPrices = [
        'subscriptions' => [
            'price_1L19DdKlaf0ZXKqlA8LR5Pvz',
            'price_1L18yZKlaf0ZXKqlJLE2VblE',
            'price_1L19BsKlaf0ZXKqlgV3i6lDK',
        ],
        'packs' => [
            'price_1L1A8iKlaf0ZXKqlB8tLjdO3',
            'price_1L1A9eKlaf0ZXKqlkMfTOOG7',
            'price_1L1AAGKlaf0ZXKqlKnJ8Fdf1',
            'price_1L1AAsKlaf0ZXKqlQ2WWVK85',
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $londonGyms = collect([
            [
                'name' => 'Clapham',
                'corporate_name' => 'clapham',
                'phone_number' => '020 3488 3813',
                'email' => 'info@clapham-gym.co.uk',
                'street_address' => '63 Wingate Square, Clapham',
                'city' => 'London',
                'postcode' => 'SW4 0AF',
                'lat' => '51.46376164223713',
                'lng' => '-0.14046097302802654',
                'operating_hours' => [
                    'friday' => [
                        '09:00',
                        '21:00',
                    ],
                    'monday' => [
                        '09:00',
                        '21:00',
                    ],
                    'sunday' => [
                        '09:00',
                        '21:00',
                    ],
                    'tuesday' => [
                        '09:00',
                        '21:00',
                    ],
                    'saturday' => [
                        '09:00',
                        '21:00',
                    ],
                    'thursday' => [
                        '09:00',
                        '21:00',
                    ],
                    'wednesday' => [
                        '09:00',
                        '21:00',
                    ],
                ],
                'trainer_break_times' => [
                    "friday" => [
                        "10:30:00",
                        "16:30:00",
                    ],
                    "monday" => [
                        "10:30:00",
                        "16:30:00",
                    ],
                    "sunday" => [
                        "12:30:00",
                    ],
                    "tuesday" => [
                        "10:30:00",
                        "16:30:00",
                    ],
                    "saturday" => [
                        "12:30:00",
                    ],
                    "thursday" => [
                        "10:30:00",
                        "16:30:00",
                    ],
                    "wednesday" => [
                        "10:30:00",
                        "16:30:00",
                    ],
                ],
                'image_path' => null,
            ],
        ])->map(fn (array $gym) => Gym::updateOrCreate(
            Arr::only($gym, 'corporate_name'),
            $gym,
        ))->pipe(function (Collection $collection) {
            $this->info($collection->count() . ' ' . Str::plural('gym', $collection->count()) . ' updated');

            return $collection;
        });

        $productIDs = $this->testModeProductIDs;
        $normalPricesIDs = $this->testModeNormalPrices;
        $londonPricesIDs = $this->testModeLondonPrices;

        if (Str::contains(config('services.stripe.secret'), 'live')) {
            $productIDs = $this->liveModeProductIDs;
            $normalPricesIDs = $this->liveModeNormalPrices;
            $londonPricesIDs = $this->liveModeLondonPrices;
        }

        foreach ($productIDs as $name => $productID) {
            SubscriptionTier::where('name', '=', $name)
                ->update(['stripe_product_id' => $productID]);

            CreditPack::where('name', '=', $name)
                ->update(['stripe_product_id' => $productID]);
        }

        $this->callSilent('update:subscription-tier-prices');
        $this->callSilent('update:credit-pack-prices');

        $normalPrices['subscriptions'] = SubscriptionTierPrice::whereIn('stripe_price_id',
            $normalPricesIDs['subscriptions'])->get('id')->pluck('id');
        $londonPrices['subscriptions'] = SubscriptionTierPrice::whereIn('stripe_price_id',
            $londonPricesIDs['subscriptions'])->get('id')->pluck('id');

        $normalPrices['packs'] = CreditPackPrice::whereIn('stripe_price_id',
            $normalPricesIDs['packs'])->get('id')->pluck('id');
        $londonPrices['packs'] = CreditPackPrice::whereIn('stripe_price_id',
            $londonPricesIDs['packs'])->get('id')->pluck('id');

        $normalGyms = Gym::whereNotIn('id', $londonGyms->pluck('id'))->get();

        $londonGyms->each(fn (Gym $gym) => $gym->subscription_tier_prices()->sync($londonPrices['subscriptions']));
        $normalGyms->each(fn (Gym $gym) => $gym->subscription_tier_prices()->sync($normalPrices['subscriptions']));

        $londonGyms->each(fn (Gym $gym) => $gym->credit_pack_prices()->sync($londonPrices['packs']));
        $normalGyms->each(fn (Gym $gym) => $gym->credit_pack_prices()->sync($normalPrices['packs']));

        return 0;
    }
}
