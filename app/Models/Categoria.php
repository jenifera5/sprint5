<?php

namespace App\Models;
use App\Models\Libro;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
     use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    // Relaciones
   public function libros()
    {
        return $this->belongsToMany(Libro::class, 'libro_categorias', 'id_categoria', 'id_libro');
    }

    
}
