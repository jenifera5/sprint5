<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Ficción', 'descripcion' => 'Obras literarias de ficción'],
            ['nombre' => 'No Ficción', 'descripcion' => 'Libros basados en hechos reales'],
            ['nombre' => 'Ciencia', 'descripcion' => 'Libros científicos y técnicos'],
            ['nombre' => 'Historia', 'descripcion' => 'Libros de historia y eventos pasados'],
            ['nombre' => 'Tecnología', 'descripcion' => 'Libros sobre tecnología e innovación'],
            ['nombre' => 'Arte', 'descripcion' => 'Libros sobre arte y cultura'],
            ['nombre' => 'Filosofía', 'descripcion' => 'Obras filosóficas y de pensamiento'],
            ['nombre' => 'Biografía', 'descripcion' => 'Historias de vida y biografías'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}