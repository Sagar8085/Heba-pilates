<?php

namespace Database\Factories;

use App\Constants\PaymentMethods;
use App\Models\CreditPackPurchase;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OrderFactory
 *
 * @package Database\Factories
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'member_id' => User::factory(),
            'value' => $this->faker->randomFloat(4),
            'method' => $this->faker->randomElement(PaymentMethods::ALL),
            'expires' => null,
            'stripe_order_id' => 'pi_' . $this->faker->words(10, true),
            'invoice_id' => null,
            'promo_code' => null,
            'orderable_id' => '',
            'orderable_type' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forSubscription()
    {
        $subscription = Subscription::factory()->create([
            'tier' => collect(Subscription::ALL)->reject(Subscription::VIP_UNLIMITED)->random(),
        ]);

        return $this->state(fn (array $attributes) => [
            'orderable_id' => $subscription->getKey(),
            'orderable_type' => Subscription::class,
        ]);
    }
}
