<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => $this->faker->company(),
            'direccion' => $this->faker->address(),
            'latitud' => $this->faker->latitude(),
            'longitud' => $this->faker->longitude(),
            'descripcion' => $this->faker->paragraph(),
        ];
    }
}
