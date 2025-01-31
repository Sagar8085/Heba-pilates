<?php

namespace Database\Factories;

use App\Models\Focus;
use Illuminate\Database\Eloquent\Factories\Factory;

class FocusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Focus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=> 'Focus',
            'slug' => 'slug',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
