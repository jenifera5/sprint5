<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Prestamo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class ActualizarPrestamoTest extends TestCase
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
    public function it_can_return_a_loan()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create(['disponibles' => 3]);
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        
        $prestamo = Prestamo::factory()->create([
            'id_usuario' => $user->id,
            'id_libro' => $libro->id,
            'estado' => 'activo',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/loans/{$prestamo->id}", [
                             'id_usuario' => $user->id,
                             'id_libro' => $libro->id,
                             'fecha_prestamo' => $prestamo->fecha_prestamo,
                             'estado' => 'devuelto',
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Prestamo actualizado correctamente']);

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'devuelto'
        ]);

        // Verificar que se incrementó el número de libros disponibles
        $this->assertDatabaseHas('libros', [
            'id' => $libro->id,
            'disponibles' => 4
        ]);
    }

    /** @test */
    public function it_prevents_returning_already_returned_loan()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $prestamo = Prestamo::factory()->create(['estado' => 'devuelto']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/loans/{$prestamo->id}", [
                             'id_usuario' => $prestamo->id_usuario,
                             'id_libro' => $prestamo->id_libro,
                             'fecha_prestamo' => $prestamo->fecha_prestamo,
                             'estado' => 'devuelto',
                         ]);

        $response->assertStatus(400)
                 ->assertJson(['error' => 'El préstamo ya fue devuelto']);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $prestamo = Prestamo::factory()->create(['estado' => 'activo']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/loans/{$prestamo->id}", [
                             'id_usuario' => $prestamo->id_usuario,
                             'id_libro' => $prestamo->id_libro,
                             'fecha_prestamo' => $prestamo->fecha_prestamo,
                             'estado' => 'devuelto',
                         ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $prestamo = Prestamo::factory()->create(['estado' => 'activo']);

        $response = $this->putJson("/api/loans/{$prestamo->id}", [
            'id_usuario' => $prestamo->id_usuario,
            'id_libro' => $prestamo->id_libro,
            'fecha_prestamo' => $prestamo->fecha_prestamo,
            'estado' => 'devuelto',
        ]);

        $response->assertStatus(401);
    }
}