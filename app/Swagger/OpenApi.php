<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="📚 Biblioteca REST API",
 *     description="API desarrollada por **Jenifer Alvarez** (Sprint 5 - IT Academy).  
 *
 * Implementa autenticación con **Laravel Passport**, control de acceso por roles  
 * y gestión de **Libros**, **Categorías** y **Préstamos**.  
 * Respuestas JSON estructuradas para integrarse con frontends o sistemas de IA.",
 *     @OA\Contact(
 *         name="Jenifer Álvarez",
 *         url="https://github.com/jenifera5"
 *     ),
 *     @OA\License(
 *         name="MIT License",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor local de desarrollo"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}
