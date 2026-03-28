<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Test de Personalidad DISC',
                'Test de Inteligencia Emocional',
                'Test de Aptitud Verbal',
                'Test de Razonamiento Numérico',
                'Test de Servicio al Cliente',
                'Test de Trabajo en Equipo',
            ]),
            'description' => $this->faker->paragraph(),
            'instructions' => 'Lea cuidadosamente cada pregunta. Seleccione la respuesta que mejor describe su comportamiento habitual.',
            'time_limit' => $this->faker->randomElement([20, 30, 45, 60, null]),
            'passing_score' => $this->faker->randomElement([60, 65, 70, 75]),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
