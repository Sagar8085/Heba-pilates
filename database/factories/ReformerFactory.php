<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\Reformer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ReformerFactory
 *
 * @package Database\Factories
 */
class ReformerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reformer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function definition(): array
    {
        return [
            'gym_id' => Gym::factory(),
            'name' => 'NU#' . $this->faker->numberBetween(1, 9999),
            'status' => 'working',
            'serial_number' => $this->createSerialNumber(),
        ];
    }

    private function createSerialNumber(): string
    {
        return $this->faker->numberBetween(10000, 99999)
            . '-'
            . Str::random(5)
            . '-'
            . $this->faker->numberBetween(10000, 99999);
    }
}
