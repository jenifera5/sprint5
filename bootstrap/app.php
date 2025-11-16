<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): Middleware {
        // ✅ AGREGAR CORS MIDDLEWARE
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Alias personalizados
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        return $middleware;
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();