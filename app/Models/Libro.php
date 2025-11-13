<?php

namespace App\Models;
use App\Models\Prestamo;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'autor', 'anio', 'disponibles'];

    // Relaciones
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'id_libro');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'libro_categorias', 'id_libro', 'id_categoria');
    }
}
