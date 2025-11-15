<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;
use App\Models\Libro;
use App\Models\Usuario;
use App\Models\Prestamo;
use Illuminate\Http\Request;


class PrestamoController extends Controller
{
 

  /**
 * @OA\Post(
 *     path="/loans",
 *     summary="Crear un nuevo préstamo",
 *     tags={"Préstamos"},
 *     security={{"passport": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id_usuario","id_libro","fecha_prestamo","estado"},
 *             @OA\Property(property="id_usuario", type="integer", example=1),
 *             @OA\Property(property="id_libro", type="integer", example=3),
 *             @OA\Property(property="fecha_prestamo", type="string", format="date", example="2025-10-10"),
 *             @OA\Property(property="fecha_devolucion", type="string", format="date", example="2025-10-20"),
 *             @OA\Property(property="estado", type="string", example="pendiente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Préstamo creado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Prestamo creado correctamente"),
 *             @OA\Property(property="prestamo", type="object",
 *                 @OA\Property(property="id", type="integer", example=2),
 *                 @OA\Property(property="id_usuario", type="integer", example=1),
 *                 @OA\Property(property="id_libro", type="integer", example=3),
 *                 @OA\Property(property="fecha_prestamo", type="string", format="date", example="2025-10-10"),
 *                 @OA\Property(property="fecha_devolucion", type="string", format="date", example="2025-10-20"),
 *                 @OA\Property(property="estado", type="string", example="pendiente")
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
           'id_usuario'=>'required|exists:usuarios,id',
           'id_libro'=>'required|exists:libros,id',
           'fecha_prestamo'=>'required|date',
           'fecha_devolucion'=>'nullable|date|after_or_equal:fecha_prestamo',
           'estado'=>'required|string|max:20',
       ]);
       $prestamo = Prestamo::create([
        'id_usuario'=>$request->id_usuario,
        'id_libro'=>$request->id_libro,
        'fecha_prestamo'=>$request->fecha_prestamo,
        'fecha_devolucion'=>$request->fecha_devolucion,
        'estado'=>$request->estado,
       ]);
       return response()->json([
        'message'=>'Prestamo creado correctamente',
        'prestamo'=> $prestamo
       ],201);
    }
}