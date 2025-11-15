<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class CrearPrestamoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $repo = new ClientRepository();
        $repo->createPersonalAccessClient(
            null,
            'Personal Access Client',
            'http://localhost'
        );
    }

    /** @test */
    public function it_can_create_a_loan()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);  // ← ADMIN
        $token = $admin->createToken('Test Token')->accessToken;
        
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $libro = Libro::factory()->create(['disponibles' => 5]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/loans', [
                             'id_usuario' => $user->id,  // ← Usuario que pide prestado
                             'id_libro' => $libro->id,
                             'fecha_prestamo' => now()->format('Y-m-d'),
                             'fecha_devolucion' => now()->addDays(7)->format('Y-m-d'),
                             'estado' => 'activo',
                         ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Prestamo creado correctamente']);

        $this->assertDatabaseHas('prestamos', [
            'id_libro' => $libro->id,
            'id_usuario' => $user->id,
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);  // ← USUARIO NORMAL
        $token = $user->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create(['disponibles' => 5]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/loans', [
                             'id_usuario' => $user->id,
                             'id_libro' => $libro->id,
                             'fecha_prestamo' => now()->format('Y-m-d'),
                             'estado' => 'activo',
                         ]);

        $response->assertStatus(403);  // ← Debe dar 403 porque no es admin
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/loans', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['id_usuario', 'id_libro', 'fecha_prestamo', 'estado']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $libro = Libro::factory()->create(['disponibles' => 5]);

        $response = $this->postJson('/api/loans', [
            'id_libro' => $libro->id,
        ]);

        $response->assertStatus(401);
    }
}