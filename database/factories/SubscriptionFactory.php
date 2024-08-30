<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SubscriptionFactory
 *
 * @package Database\Factories
 */
class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tier' => $this->faker->randomElement(Subscription::ALL),
            'expires' => $this->faker->dateTimeBetween(now(), now()->addYear()),
            'renew' => 1,
            'online_credits' => 0,
            'studio_credits' => 0,
            'billing_type' => 'manual',
            'stripe_id' => null,
            'stripe_payment_intent' => $this->faker->word,
            'pause_days' => null,
        ];
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function vipUnlimited()
    {
        return $this->state(function () {
            return [
                'tier' => Subscription::VIP_UNLIMITED,
            ];
        });
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function unlimitedMonthly()
    {
        return $this->state(function () {
            return [
                'tier' => Subscription::UNLIMITED_MEMBERSHIP_SUBSCRIPTION,
            ];
        });
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function premiumMonthly()
    {
        return $this->state(function () {
            return [
                'tier' => Subscription::PREMIUM_MEMBERSHIP_SUBSCRIPTION,
            ];
        });
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function standard()
    {
        return $this->state(function () {
            return [
                'tier' => Subscription::STANDARD,
            ];
        });
    }
}
