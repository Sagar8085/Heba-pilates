<?php

namespace Database\Factories;

use App\Models\IntensityMET;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntensityMETFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IntensityMET::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'intensity'=> 'intensity',
            'met_value' => 1.0
        ];
    }
}
