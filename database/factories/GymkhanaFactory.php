<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GymkhanaFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
        ];
    }
}
