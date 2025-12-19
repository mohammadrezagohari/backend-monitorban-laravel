<?php

namespace Modules\Room\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServerRoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Room\Models\ServerRoom::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

