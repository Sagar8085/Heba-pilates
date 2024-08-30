<?php

namespace Database\Factories;

use App\Constants\PaymentMethods;
use App\Models\CreditPack;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CreditPackFactory
 *
 * @package Database\Factories
 */
class CreditPackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditPack::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(3),
            'online_credits' => $this->faker->numberBetween(0,9),
            'studio_credits' => $this->faker->numberBetween(0,9),
            'stripe_price_id' => null,
            'apple_product_id' => null,
            'google_product_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
