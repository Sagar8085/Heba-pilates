<?php

namespace Database\Factories;

use App\Models\OnDemandCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnDemandCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OnDemandCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name;

        return [
            'name' => $name,
            'slug' => strtolower(str_replace([' ', '.'], '-', $name)),
            'description' => $this->faker->text($maxNbChars = 200),
            'image_path' => $this->faker->file($sourceDir = public_path('/images'), $targetDir = '/tmp'),
            'created_by' => 1,
        ];
    }
}
