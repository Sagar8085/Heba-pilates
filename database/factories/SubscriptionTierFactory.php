<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\SubscriptionTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class SubscriptionTierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriptionTier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tier = Arr::random(Subscription::ALL);

        return [
            'name' => $tier,
            'slug' => $tier,
            'price' => 0,
            'product_group' => 0,
            'product_id' => 0,
            'online_credits' => 0,
            'studio_credits' => 0,
        ];
    }
}
