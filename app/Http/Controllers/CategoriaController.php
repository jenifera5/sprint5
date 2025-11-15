<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;
use App\Models\Libro;
use App\Models\Categoria;
use Illuminate\Http\Request;


class CategoriaController extends Controller
{


/**
 * @OA\Post(
 *     path="/categories",
 *     summary="Crear una nueva categoría",
 *     tags={"Categorías"},
 *     security={{"passport": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nombre", "descripcion"},
 *             @OA\Property(property="nombre", type="string", example="Ciencia ficción"),
 *             @OA\Property(property="descripcion", type="string", example="Libros futuristas o tecnológicos")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Categoría creada correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Categoria creada correctamente"),
 *             @OA\Property(property="categoria", type="object",
 *                 @OA\Property(property="id", type="integer", example=2),
 *                 @OA\Property(property="nombre", type="string", example="Ciencia ficción"),
 *                 @OA\Property(property="descripcion", type="string", example="Libros futuristas o tecnológicos")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Datos inválidos"
 *     )
 * )
 */


    public function store(Request $request)
    {
        $request->validate([
            'nombre' =>'required|string|max:45',
            'descripcion'=>'required|string|max:100',
        ]);
        $categoria = Categoria::create([
            'nombre'=>$request->nombre,
            'descripcion'=>$request->descripcion,
        ]);
        
        return response()->json([
            'message' => 'Categoria creada correctamente',
            'categoria'=>$categoria
        ],201);
    }
}
   