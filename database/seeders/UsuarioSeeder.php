<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'nombre' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
            'rol' => 'admin',
        ]);

        Usuario::create([
            'nombre' => 'Usuario Normal',
            'email' => 'user@test.com',
            'password' => Hash::make('123456'),
            'rol' => 'usuario',
        ]);

        // Usuarios adicionales usando factory
        Usuario::factory()->count(3)->create();
    }
}
