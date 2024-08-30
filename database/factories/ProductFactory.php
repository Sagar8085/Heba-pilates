<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ProductFactory
 *
 * @package Database\Factories
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => Str::of($this->faker->words(4, true))->ucfirst(),
            'price' => $this->faker->randomNumber(2) * 100,
            'active' => $this->faker->boolean,
            'created_at' => $this->faker->dateTimeBetween(),
            'updated_at' => $this->faker->dateTimeBetween(),
        ];
    }
}
