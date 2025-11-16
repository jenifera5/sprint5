<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $usuario = $request->user();

        // Si NO hay usuario autentificado o su rol NO está permitido
        if (!$usuario || !in_array($usuario->rol, $roles)) {
            return response()->json(['error' => 'Acceso denegado: no tienes permiso.'], 403);
        }

        // Si pasa el filtro, continúa la petición
        return $next($request);
    }
}
