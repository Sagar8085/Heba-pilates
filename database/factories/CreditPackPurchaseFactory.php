<?php

namespace Database\Factories;

use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditPackPurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditPackPurchase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'credit_pack_id' => CreditPack::factory(),
            'order_id' => fn ($data) => Order::factory(['member_id' => $data['user_id']]),
            'online_credits' => $this->faker->numberBetween(0, 9),
            'studio_credits' => $this->faker->numberBetween(0, 9),
        ];
    }
}
