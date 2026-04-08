<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class UpdatePositionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Eliminar ──────────────────────────────────────────────────────
        Position::where('name', 'Supervisor de Piso')->delete();

        // ── Agregar (solo si no existe) ───────────────────────────────────
        $nuevos = [
            ['name' => 'Chef',                       'description' => 'Responsable de la cocina y preparación de alimentos'],
            ['name' => 'Auxiliar Contable',           'description' => 'Apoyo en procesos contables y administrativos'],
            ['name' => 'Steward',                     'description' => 'Limpieza y organización de cocina y utensilios'],
            ['name' => 'Barman',                      'description' => 'Preparación y servicio de bebidas en barra'],
            ['name' => 'Maître',                      'description' => 'Coordinación del servicio de sala y atención a huéspedes'],
            ['name' => 'Capitán de Meseros',          'description' => 'Supervisión del equipo de meseros durante el servicio'],
            ['name' => 'Camarera',                    'description' => 'Limpieza y arreglo de habitaciones'],
            ['name' => 'Supervisor de Habitaciones',  'description' => 'Supervisión del área de housekeeping y habitaciones'],
            ['name' => 'Salvavidas',                  'description' => 'Vigilancia y seguridad en zonas de piscina y playa'],
            ['name' => 'Piscinero',                   'description' => 'Mantenimiento y limpieza de piscinas'],
            ['name' => 'Recreacionista',              'description' => 'Planificación y ejecución de actividades recreativas'],
        ];

        foreach ($nuevos as $data) {
            Position::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }

        $this->command->info('✓ Supervisor de Piso eliminado.');
        $this->command->info('✓ ' . count($nuevos) . ' cargos nuevos agregados.');
    }
}
