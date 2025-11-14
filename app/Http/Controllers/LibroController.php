<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class  LibroController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/books",
     *   tags={"Libros"},
     *   summary="Listar todos los libros",
     *   description="Devuelve un listado de todos los libros con sus categorías.",
     *   @OA\Response(
     *       response=200,
     *       description="Listado de libros obtenido correctamente",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *               @OA\Property(property="id", type="integer", example=1),
     *               @OA\Property(property="titulo", type="string", example="Dune"),
     *               @OA\Property(property="autor", type="string", example="Frank Herbert"),
     *               @OA\Property(property="anio", type="integer", example=1965),
     *               @OA\Property(property="disponibles", type="integer", example=5),
     *               @OA\Property(
     *                   property="categorias",
     *                   type="array",
     *                   @OA\Items(
     *                       @OA\Property(property="id", type="integer", example=2),
     *                       @OA\Property(property="nombre", type="string", example="Ciencia Ficción")
     *                   )
     *               )
     *           )
     *       )
     *   )
     * )
     */

    public function index()
    {
        $books = Libro::with('categorias')->get();
        return response()->json([
            'message' => 'Listado de libros con categorías obtenido correctamente',
            'data' => $books
        ]);
    }

    // 🔹 Crear libro (solo admin)
    /**
 * @OA\Post(
 *     path="/books",
 *     summary="Crear un nuevo libro",
 *     tags={"Libros"},
 *     security={{"passport": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"titulo","autor","anio","disponibles"},
 *             @OA\Property(property="titulo", type="string", example="La sombra del viento"),
 *             @OA\Property(property="autor", type="string", example="Carlos Ruiz Zafón"),
 *             @OA\Property(property="anio", type="integer", example=2001),
 *             @OA\Property(property="disponibles", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Libro creado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Libro creado correctamente"),
 *             @OA\Property(
 *                 property="libro",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="titulo", type="string", example="La sombra del viento"),
 *                 @OA\Property(property="autor", type="string", example="Carlos Ruiz Zafón"),
 *                 @OA\Property(property="anio", type="integer", example=2001),
 *                 @OA\Property(property="disponibles", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Datos inválidos o faltantes"
 *     )
 * )
 */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'titulo' => 'required|string|max:100',
            'autor' => 'required|string|max:100',
            'anio' => 'required|digits:4|integer|min:1000|max:' . now()->year,
            'disponibles' => 'required|integer|min:0',
            'categorias' => 'array' // IDs de categorías opcionales
        ]);

        $book = Libro::create([
            'titulo' => $request->titulo,
            'autor' => $request->autor,
            'anio' => $request->anio,
            'disponibles' => $validated['disponibles'],
           
        ]);

          // Vincular categorías si existen
        if (!empty($validated['categorias'])) {
            $book->categorias()->sync($validated['categorias']);
        }

        return response()->json([
            'message' => 'Libro creado correctamente',
            'libro' => $book->load('categorias'),
        ], 201);
    }


    
}