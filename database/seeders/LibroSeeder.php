<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Libro;
use App\Models\Categoria;

class LibroSeeder extends Seeder
{
    public function run(): void
    {
        $libros = [
            [
                'titulo' => 'Cien Años de Soledad',
                'autor' => 'Gabriel García Márquez',
                'anio' => 1967,
                'disponibles' => 5,
                'categorias' => ['Ficción']
            ],
            [
                'titulo' => 'El Quijote',
                'autor' => 'Miguel de Cervantes',
                'anio' => 1605,
                'disponibles' => 3,
                'categorias' => ['Ficción', 'Historia']
            ],
            [
                'titulo' => 'Sapiens',
                'autor' => 'Yuval Noah Harari',
                'anio' => 2014,
                'disponibles' => 4,
                'categorias' => ['No Ficción', 'Historia']
            ],
            [
                'titulo' => 'El Origen de las Especies',
                'autor' => 'Charles Darwin',
                'anio' => 1859,
                'disponibles' => 2,
                'categorias' => ['Ciencia', 'No Ficción']
            ],
            [
                'titulo' => '1984',
                'autor' => 'George Orwell',
                'anio' => 1949,
                'disponibles' => 6,
                'categorias' => ['Ficción']
            ],
        ];

        foreach ($libros as $libroData) {
            $categoriasNombres = $libroData['categorias'];
            unset($libroData['categorias']);

            $libro = Libro::create($libroData);

            // Asociar categorías
            $categorias = Categoria::whereIn('nombre', $categoriasNombres)->get();
            $libro->categorias()->attach($categorias);
        }

        // Libros adicionales usando factory
        Libro::factory()->count(10)->create()->each(function ($libro) {
            $libro->categorias()->attach(
                Categoria::inRandomOrder()->limit(rand(1, 3))->pluck('id')
            );
        });
    }
}