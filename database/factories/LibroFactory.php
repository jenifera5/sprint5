<?php

namespace Database\Factories;
use App\Models\Libro;

use Illuminate\Database\Eloquent\Factories\Factory;


class LibroFactory extends Factory
{
    protected $model = Libro::class;

    public function definition(): array
    {
        return [
            'titulo'=> $this->faker->sentence(3),
            'autor'=> $this->faker->name,
            'anio'=>$this->faker->numberBetween(1950, now()->year),
            'disponibles'=>$this->faker->numberBetween(1,10),
        ];
    }
}
