<?php

namespace Tests\Feature;

use Tests\TestCase; 
use App\Models\Usuario;
use App\Models\Prestamo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class EliminarPrestamoTest extends TestCase
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
    public function it_can_delete_a_loan()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $prestamo = Prestamo::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/loans/{$prestamo->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Prestamo eliminado correctamente']);

        $this->assertDatabaseMissing('prestamos', ['id' => $prestamo->id]);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $prestamo = Prestamo::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/loans/{$prestamo->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_404_if_loan_not_found()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/loans/99999");

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Prestamo no encontrado']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $prestamo = Prestamo::factory()->create();

        $response = $this->deleteJson("/api/loans/{$prestamo->id}");

        $response->assertStatus(401);
    }
}