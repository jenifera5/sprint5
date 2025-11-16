<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Models\Libro;

class PrestamoSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = Usuario::where('email', 'user@test.com')->first();
        $libros = Libro::limit(5)->get();

        // Préstamo activo
        Prestamo::create([
            'id_usuario' => $usuario->id,
            'id_libro' => $libros[0]->id,
            'fecha_prestamo' => now()->subDays(5),
            'fecha_devolucion' => now()->addDays(10),
            'estado' => 'activo',
        ]);

        // Préstamo devuelto
        Prestamo::create([
            'id_usuario' => $usuario->id,
            'id_libro' => $libros[1]->id,
            'fecha_prestamo' => now()->subDays(20),
            'fecha_devolucion' => now()->subDays(5),
            'estado' => 'devuelto',
        ]);

        // Préstamos adicionales usando factory
        Prestamo::factory()->count(8)->create();
    }
}
