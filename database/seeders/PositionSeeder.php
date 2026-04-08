<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Mesero',                   'description' => 'Atención al cliente en sala de restaurant'],
            ['name' => 'Auxiliar de Cocina',        'description' => 'Apoyo en preparación de alimentos y limpieza de cocina'],
            ['name' => 'Auxiliar de Mantenimiento', 'description' => 'Mantenimiento preventivo y correctivo de instalaciones'],
            ['name' => 'Caddie',                    'description' => 'Asistente de campo de golf'],
            ['name' => 'Recepcionista',             'description' => 'Atención al cliente, reservas y gestión administrativa'],
            ['name' => 'Cajero',                    'description' => 'Manejo de caja registradora y pagos'],
            ['name' => 'Chef',                      'description' => 'Responsable de la cocina y preparación de alimentos'],
            ['name' => 'Auxiliar Contable',         'description' => 'Apoyo en procesos contables y administrativos'],
            ['name' => 'Steward',                   'description' => 'Limpieza y organización de cocina y utensilios'],
            ['name' => 'Barman',                    'description' => 'Preparación y servicio de bebidas en barra'],
            ['name' => 'Maître',                    'description' => 'Coordinación del servicio de sala y atención a huéspedes'],
            ['name' => 'Capitán de Meseros',        'description' => 'Supervisión del equipo de meseros durante el servicio'],
            ['name' => 'Camarera',                  'description' => 'Limpieza y arreglo de habitaciones'],
            ['name' => 'Supervisor de Habitaciones','description' => 'Supervisión del área de housekeeping y habitaciones'],
            ['name' => 'Salvavidas',                'description' => 'Vigilancia y seguridad en zonas de piscina y playa'],
            ['name' => 'Piscinero',                 'description' => 'Mantenimiento y limpieza de piscinas'],
            ['name' => 'Recreacionista',            'description' => 'Planificación y ejecución de actividades recreativas'],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['name' => $position['name']],
                array_merge($position, ['is_active' => true])
            );
        }
    }
}
