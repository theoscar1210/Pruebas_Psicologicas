<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $mesero = Position::where('name', 'Mesero')->first();
        $recepcion = Position::where('name', 'Recepcionista')->first();

        $candidates = [
            ['name' => 'Carlos Andrés Gómez', 'email' => 'carlos.gomez@email.com', 'phone' => '3001234567', 'document_number' => '1020304050', 'position_id' => $mesero?->id],
            ['name' => 'María Fernanda López', 'email' => 'maria.lopez@email.com', 'phone' => '3119876543', 'document_number' => '1030405060', 'position_id' => $mesero?->id],
            ['name' => 'Juan Sebastián Rojas', 'email' => 'juan.rojas@email.com', 'phone' => '3205678901', 'document_number' => '1040506070', 'position_id' => $recepcion?->id],
        ];

        foreach ($candidates as $data) {
            Candidate::create(array_merge($data, [
                'status' => 'active',
                'created_by' => $admin->id,
                // access_code se genera automáticamente en el boot() del modelo
            ]));
        }
    }
}
