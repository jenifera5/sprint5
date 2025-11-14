<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;




class AuthController extends Controller
{
  /**
 * @OA\Post(
 *     path="/register",
 *     summary="Registrar un nuevo usuario",
 *     tags={"Autentificación"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nombre","email","password"},
 *             @OA\Property(property="nombre", type="string", example="Jenifer"),
 *             @OA\Property(property="email", type="string", format="email", example="jenifer@example.com"),
 *             @OA\Property(property="password", type="string", example="123456"),
 *             @OA\Property(property="rol", type="string", example="admin", description="Opcional: admin o usuario")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Usuario registrado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Usuario registrado correctamente"),
 *             @OA\Property(property="usuario", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nombre", type="string", example="Jenifer"),
 *                 @OA\Property(property="email", type="string", example="jenifer@example.com"),
 *                 @OA\Property(property="rol", type="string", example="admin")
 *             ),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJh...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Datos inválidos o email duplicado"
 *     )
 * )
 */


    public function register(Request $request)
    {
        $request->validate([

            'nombre'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:usuarios',
            'password'=>'required|string|min:6|confirmed',
            'rol'      => 'in:admin,usuario'
        ]);
        $usuario = Usuario::create([
            'nombre'=>$request->nombre,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'rol'      => $request->rol ?? 'usuario',

        ]);
        $token =$usuario->createToken('TokenPersonal')->accessToken;
        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'usuario' => $usuario,
            'token'   => $token,
        ], 201);

    }


        /**
 * @OA\Post(
 *     path="/login",
 *     summary="Iniciar sesión de usuario",
 *     tags={"Autentificación"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="jenifer@example.com"),
 *             @OA\Property(property="password", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inicio de sesión exitoso",
 *         @OA\JsonContent(
 *             @OA\Property(property="usuario", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nombre", type="string", example="Jenifer"),
 *                 @OA\Property(property="email", type="string", example="jenifer@example.com"),
 *                 @OA\Property(property="rol", type="string", example="admin")
 *             ),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJh...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciales incorrectas"
 *     )
 * )
 */


   public function login(Request $request)
{
    $credenciales = $request->only('email', 'password');
    
    if (!Auth::attempt($credenciales)) {
        return response()->json(['error' => 'Credenciales incorrectas'], 401);  // ← UNA SOLA LÍNEA
    }
    
    $usuario = Auth::user();
    $token = $usuario->createToken('API Token')->accessToken;
    
    return response()->json([
        'usuario' => $usuario,
        'token' => $token,
    ]);
}
}    