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
    // 🔹 Actualizar libro (solo admin)
    /**
 * @OA\Put(
 *     path="/books/{id}",
 *     summary="Actualizar un libro existente",
 *     tags={"Libros"},
 *     security={{"passport": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del libro a actualizar",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"titulo","autor","anio","disponibles"},
 *             @OA\Property(property="titulo", type="string", example="El juego del ángel"),
 *             @OA\Property(property="autor", type="string", example="Carlos Ruiz Zafón"),
 *             @OA\Property(property="anio", type="integer", example=2008),
 *             @OA\Property(property="disponibles", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Libro actualizado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Libro actualizado correctamente"),
 *             @OA\Property(
 *                 property="libro",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="titulo", type="string", example="El juego del ángel"),
 *                 @OA\Property(property="autor", type="string", example="Carlos Ruiz Zafón"),
 *                 @OA\Property(property="anio", type="integer", example=2008),
 *                 @OA\Property(property="disponibles", type="integer", example=3)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Libro no encontrado"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Datos inválidos o faltantes"
 *     )
 * )
 */


    public function update(Request $request, string $id)
    {
       $book = Libro::find($id);
        if (!$book) {
            return response()->json(['error' => 'Libro no encontrado'], 404);
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:100',
            'autor' => 'required|string|max:100',
            'anio' => 'required|digits:4|integer|min:1000|max:' . now()->year,
            'disponibles' => 'required|integer|min:0',
            'categorias' => 'array' // opcional
        ]);

        $book->update([
            'titulo' => $validated['titulo'],
            'autor' => $validated['autor'],
            'anio' => $validated['anio'],
            'disponibles' => $validated['disponibles'],
        ]);

        if (isset($validated['categorias'])) {
            $book->categorias()->sync($validated['categorias']);
        }

        return response()->json([
            'message' => 'Libro actualizado correctamente',
            'libro' => $book->load('categorias'),
        ]);
    }
      // 🔹 Eliminar libro (solo admin)
    /**
 * @OA\Delete(
 *     path="/books/{id}",
 *     summary="Eliminar un libro por ID",
 *     tags={"Libros"},
 *     security={{"passport": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del libro a eliminar",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Libro eliminado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Libro eliminado correctamente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Libro no encontrado"
 *     )
 * )
 */
    public function destroy(string $id)
    {
        $book = Libro::find($id);
        if (!$book) {
            return response()->json(['error' => 'Libro no encontrado'], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'Libro eliminado correctamente'
        ]);
    }
   // 🔹 Buscar libro por título o autor
    /**
 * @OA\Get(
 *     path="/books/search",
 *     summary="Buscar libros por título o autor",
 *     tags={"Libros"},
 *     security={{"passport": {}}},
 *     @OA\Parameter(
 *         name="query",
 *         in="query",
 *         required=true,
 *         description="Término de búsqueda (título o autor)",
 *         @OA\Schema(type="string", example="Cervantes")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Resultados obtenidos correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Resultados obtenidos correctamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="titulo", type="string", example="Don Quijote de la Mancha"),
 *                     @OA\Property(property="autor", type="string", example="Miguel de Cervantes"),
 *                     @OA\Property(property="anio", type="integer", example=1605),
 *                     @OA\Property(property="disponibles", type="integer", example=4)
 *                 )
 *             )
 *         )
 *     )
 * )
 */


    public function search(Request $request)
    {
         $query = $request->input('query');

        $results = Libro::with('categorias')
            ->where('titulo', 'like', "%{$query}%")
            ->orWhere('autor', 'like', "%{$query}%")
            ->get();

        return response()->json([
            'message' => 'Resultados obtenidos correctamente',
            'data' => $results,
        ]);
        
    }
    
}