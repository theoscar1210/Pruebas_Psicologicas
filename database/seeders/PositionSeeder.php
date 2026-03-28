<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Mesero', 'description' => 'Atención al cliente en sala de restaurant'],
            ['name' => 'Auxiliar de Cocina', 'description' => 'Apoyo en preparación de alimentos y limpieza de cocina'],
            ['name' => 'Auxiliar de Mantenimiento', 'description' => 'Mantenimiento preventivo y correctivo de instalaciones'],
            ['name' => 'Caddie', 'description' => 'Asistente de campo de golf'],
            ['name' => 'Recepcionista', 'description' => 'Atención al cliente, reservas y gestión administrativa'],
            ['name' => 'Cajero', 'description' => 'Manejo de caja registradora y pagos'],
            ['name' => 'Supervisor de Piso', 'description' => 'Supervisión del personal operativo en sala'],
        ];

        foreach ($positions as $position) {
            Position::create(array_merge($position, ['is_active' => true]));
        }
    }
}
