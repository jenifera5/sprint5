<?php

namespace Database\Factories;

use App\Models\Prestamo;
use App\Models\Libro;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrestamoFactory extends Factory
{
    protected $model = Prestamo::class;

    public function definition(): array
    {
        return [
            'id_libro' => Libro::factory(),     // crea un libro válido
            'id_usuario' => Usuario::factory(), // crea un usuario válido
            'fecha_prestamo' => now(),
            'fecha_devolucion' => now()->addDays(7),
            'estado' => 'activo',               // puedes cambiarlo si tu tabla lo requiere
        ];
    }
}
