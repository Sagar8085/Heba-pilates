<?php

namespace Database\Factories;

use App\Models\Reformer;
use App\Models\ReformerBooking;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ReformerBookingFactory
 *
 * @package Database\Factories
 */
class ReformerBookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReformerBooking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reformer_id' => Reformer::factory(),
            'datetime' => $this->faker->dateTimeBetween(),
            'bookable_id' => $this->faker->numberBetween(1, 9999),
            'bookable_type' => Subscription::class,
        ];
    }
}
