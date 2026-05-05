<?php

use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();

        Test::updateOrCreate(
            ['test_type' => 'tsc_sl_h'],
            [
                'name'             => 'TSC-SL Hospitalidad — Servicio de Mesa y F&B',
                'description'      => 'Variante del TSC-SL adaptada para roles de servicio de alimentos y bebidas: mesero, maître, barman, cajero, camarera y capitán de meseros. Evalúa las 6 competencias de servicio (Empatía, Comunicación, Resolución, Clientes Difíciles, Proactividad, Regulación Emocional) en escenarios propios del entorno de restaurante y bar.',
                'instructions'     => "Esta prueba evalúa sus competencias de servicio al cliente en el contexto de hospitalidad y servicio de mesa.\n\nConsta de 3 módulos:\n1. Juicio Situacional (20 ítems): situaciones reales de restaurante/bar\n2. Escala de Actitudes (40 ítems): afirmaciones sobre su forma de trabajar\n3. Escenarios Abiertos (3 situaciones): descripción detallada de cómo actuaría\n\nResponda con honestidad. No hay respuestas correctas o incorrectas en apariencia.",
                'module'           => 'competencias',
                'evaluator_scored' => false,
                'scoring_method'   => 'dimensional',
                'time_limit'       => 50,
                'passing_score'    => 124,
                'is_active'        => true,
                'created_by'       => $admin?->id,
            ]
        );
    }

    public function down(): void
    {
        Test::where('test_type', 'tsc_sl_h')->delete();
    }
};
