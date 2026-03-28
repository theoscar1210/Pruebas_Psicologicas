<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    public function definition(): array
    {
        $positions = [
            'Mesero', 'Auxiliar de Cocina', 'Auxiliar de Mantenimiento',
            'Caddie', 'Recepcionista', 'Cajero', 'Supervisor de Piso',
            'Chef', 'Barman', 'Asistente Administrativo',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($positions),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
