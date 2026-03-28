<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador principal
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@pruebas.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Usuario de RRHH
        User::create([
            'name' => 'Recursos Humanos',
            'email' => 'rrhh@pruebas.com',
            'password' => Hash::make('password'),
            'role' => 'hr',
            'is_active' => true,
        ]);
    }
}
