# 📚 Library Management System - Backend API

> API REST con Laravel 10 + Laravel Passport 12

Sistema de gestión de biblioteca con autenticación JWT mediante Laravel Passport, control de roles (admin/usuario), gestión completa de préstamos, búsqueda avanzada y documentación Swagger interactiva.

![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php&logoColor=white)
![Passport](https://img.shields.io/badge/Passport-12.4-green?style=flat)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat&logo=mysql&logoColor=white)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Tecnologías](#️-tecnologías)
- [Requisitos Previos](#-requisitos-previos)
- [Instalación](#-instalación)
- [Configuración](#️-configuración)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Documentación API](#-documentación-api)
- [Autenticación con Passport](#-autenticación-con-passport)
- [Testing](#-testing)
- [Licencia](#-licencia)

---

## ✨ Características

- **Autenticación OAuth2** con Laravel Passport 12
  - Tokens de acceso personales
  - Revocación de tokens en logout
  - Expiración configurable (1 hora por defecto)
  
- **Sistema de Roles** 
  - Middleware `CheckRole` personalizado
  - Roles: `admin` y `usuario`
  - Control de acceso granular por endpoint
  
- **CRUD Completo**
  - 📚 Libros: crear, leer, actualizar, eliminar
  - 🏷️ Categorías: gestión completa con relaciones
  - 📖 Préstamos: registro y seguimiento de estado
  
- **Endpoints Avanzados**
  - `/api/books/search?query=` - Búsqueda por título o autor
  - `/api/books/stats/popular` - Top 5 libros más prestados
  
- **Documentación Swagger/OpenAPI 3.0**
  - Interfaz interactiva en `/api/documentation`
  - Autenticación Bearer integrada
  - Ejemplos de peticiones y respuestas
  
- **Testing con PHPUnit**
  - Tests de autenticación (registro, login, logout)
  - Tests de controladores (libros, categorías, préstamos)
  - Cobertura de casos de éxito y error
  - Uso de factories para datos de prueba

---

## 🛠️ Tecnologías

- **Laravel 10.22** - Framework PHP
- **PHP 8.2** - Lenguaje del servidor
- **Laravel Passport 12.4.2** - Autenticación OAuth2
- **MySQL 8** - Base de datos
- **PHPUnit** - Testing
- **L5-Swagger** - Documentación OpenAPI
- **Git** - Control de versiones

---

## 📦 Requisitos Previos

Asegúrate de tener instalado:

| Software | Versión Mínima | Verificar |
|----------|----------------|-----------|
| PHP      | 8.2            | `php -v` |
| Composer | 2.x            | `composer -V` |
| MySQL    | 8.0            | `mysql --version` |
| Git      | 2.x            | `git --version` |

**Extensiones PHP requeridas:**
- `sodium` (para Passport)
- `pdo_mysql`
- `openssl`
- `mbstring`
- `tokenizer`
- `xml`
- `json`

Para activar `sodium`, edita tu `php.ini` y descomenta:
```ini
extension=sodium
```

---

## 🚀 Instalación

### Paso 1: Clonar el repositorio

```bash
git clone https://github.com/jenifera5/sprint5.git
cd sprint5
```

### Paso 2: Instalar dependencias

```bash
composer install
```

### Paso 3: Configurar entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### Paso 4: Configurar base de datos

Edita el archivo `.env` con tus credenciales:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca_api
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos:

```bash
mysql -u root -p
CREATE DATABASE biblioteca_api;
EXIT;
```

### Paso 5: Ejecutar migraciones

```bash
# Ejecutar migraciones
php artisan migrate

# (Opcional) Poblar con datos de prueba
php artisan db:seed
```

### Paso 6: Instalar y configurar Passport

```bash
# Instalar Passport 12 (compatible con PHP 8.2)
composer require laravel/passport:^12.4.2 --with-all-dependencies

# Ejecutar migraciones de Passport
php artisan migrate

# Instalar clientes OAuth2
php artisan passport:install
```

**Importante:** Guarda los Client ID y Secret mostrados. Laravel Passport 12 **NO** requiere `Passport::routes()` en el `AuthServiceProvider`.

### Paso 7: Verificar configuración de autenticación

Asegúrate de que `config/auth.php` contenga:

```php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\Usuario::class,
    ],
],
```

### Paso 8: Generar documentación Swagger

```bash
# Instalar L5-Swagger
composer require "darkaonline/l5-swagger"

# Publicar configuración
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# Generar documentación
php artisan l5-swagger:generate
```

### Paso 9: Iniciar servidor

```bash
php artisan serve
```

El backend estará disponible en: `http://127.0.0.1:8000`

---

## ⚙️ Configuración

### CORS

El archivo `config/cors.php` está configurado para permitir peticiones desde el frontend:

```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

### Tokens de Passport

En `app/Providers/AuthServiceProvider.php`:

```php
use Laravel\Passport\Passport;

public function boot(): void
{
    Passport::tokensExpireIn(now()->addHours(1));
    Passport::refreshTokensExpireIn(now()->addDays(7));
    Passport::personalAccessTokensExpireIn(now()->addMonths(6));
}
```

---

## 📂 Estructura del Proyecto

```
sprint5/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── LibroController.php
│   │   │   ├── CategoriaController.php
│   │   │   └── PrestamoController.php
│   │   └── Middleware/
│   │       └── CheckRole.php
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Libro.php
│   │   ├── Categoria.php
│   │   └── Prestamo.php
│   └── Swagger/
│       └── OpenApi.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
│   └── Feature/
└── storage/
    └── api-docs/
        └── api-docs.json
```

---

## 📖 Documentación API

### Acceso a Swagger UI

Una vez iniciado el servidor, accede a:

```
http://127.0.0.1:8000/api/documentation
```

### Endpoints Principales

#### Autenticación

```http
POST /api/register
POST /api/login
POST /api/logout
```

#### Libros

```http
GET    /api/books
POST   /api/books                    (admin)
GET    /api/books/search?query=
GET    /api/books/stats/popular
PUT    /api/books/{id}               (admin)
DELETE /api/books/{id}               (admin)
```

#### Categorías

```http
GET    /api/categories
POST   /api/categories               (admin)
PUT    /api/categories/{id}          (admin)
DELETE /api/categories/{id}          (admin)
```

#### Préstamos

```http
GET    /api/loans
POST   /api/loans                    (admin)
PUT    /api/loans/{id}               (admin)
DELETE /api/loans/{id}               (admin)
```

---

## 🔐 Autenticación con Passport

### Registro

```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nombre": "Jenifer",
    "email": "jenifer@example.com",
    "password": "123456",
    "rol": "admin"
  }'
```

**Respuesta:**
```json
{
  "message": "Usuario registrado correctamente",
  "usuario": {
    "id": 1,
    "nombre": "Jenifer",
    "email": "jenifer@example.com",
    "rol": "admin"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### Uso del Token

```bash
curl -X GET http://127.0.0.1:8000/api/books \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {tu_token}"
```

---

## 🧪 Testing

### Ejecutar todos los tests

```bash
php artisan test
```

### Ejecutar tests específicos

```bash
# Tests de autenticación
php artisan test --filter RegisterTest
php artisan test --filter LoginTest

# Tests de libros
php artisan test --filter LibroControllerTest

# Tests de categorías
php artisan test --filter CategoriaControllerTest

# Tests de préstamos
php artisan test --filter PrestamoControllerTest
```

### Cobertura de Tests

| Controlador | Tests | Cobertura |
|-------------|-------|-----------|
| AuthController | Registro, Login, Logout | 100% |
| LibroController | CRUD + Search + Popular | 100% |
| CategoriaController | CRUD completo | 100% |
| PrestamoController | CRUD completo | 100% |

---

## 📄 Licencia

Este proyecto está bajo la **Licencia MIT**. Ver archivo `LICENSE` para más detalles.

```
MIT License

Copyright (c) 2025 Jenifer Álvarez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 👩‍💻 Autora

**Jenifer Álvarez**

Proyecto desarrollado como parte del **Sprint 5 - API REST con Laravel Passport** del curso **FullStack** de **IT Academy**.

### Contacto

- **GitHub:** [@jenifera5](https://github.com/jenifera5)
- **Proyecto:** [Sprint 5 - Biblioteca REST API](https://github.com/jenifera5/sprint5)

---

## 🙏 Agradecimientos

- **IT Academy** - Por el programa FullStack y la guía durante el sprint
- **Laravel** - Por el excelente framework PHP
- **Laravel Passport** - Por simplificar la autenticación OAuth2
- **Claude (Anthropic)** - Por la asistencia con IA generativa durante el desarrollo

---

## 📝 Notas Técnicas

### Configuraciones Importantes

**Passport 12 - Cambios clave:**
- ❌ **NO** usar `Passport::routes()` en `AuthServiceProvider`
- ✅ Las rutas OAuth2 se registran automáticamente
- ✅ Configurar expiración de tokens en `AuthServiceProvider`

**Middleware de Roles:**
- Registrado en `app/Http/Kernel.php` como `'role' => CheckRole::class`
- Uso: `Route::middleware('role:admin')`
- Permite múltiples roles: `middleware('role:admin,usuario')`

### Errores Comunes y Soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| `Call to undefined method Passport::routes()` | Passport 12 no usa este método | Eliminarlo del `AuthServiceProvider` |
| `401 Unauthenticated` | Token no enviado o inválido | Verificar header `Authorization: Bearer {token}` |
| `403 Forbidden` | Usuario sin rol adecuado | Verificar rol del usuario y middleware |
| `password truncated` | Campo password < 255 chars | Migración: `$table->string('password', 255)` |
| `extension sodium` | Extensión no activada | Descomentar `extension=sodium` en `php.ini` |

---

**Última actualización:** Noviembre 2025 | **Versión:** 1.0.0
