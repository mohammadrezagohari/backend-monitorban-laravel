<?php

namespace Modules\Sensor\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SensorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Sensor\Models\Sensor::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

