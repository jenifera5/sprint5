<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Prestamo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class ListarPrestamosTest extends TestCase
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
    public function it_can_list_all_loans_as_admin()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $user1 = Usuario::factory()->create(['rol' => 'usuario']);
        $user2 = Usuario::factory()->create(['rol' => 'usuario']);

        Prestamo::factory()->count(2)->create(['id_usuario' => $user1->id]);
        Prestamo::factory()->count(1)->create(['id_usuario' => $user2->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/loans');

        $response->assertStatus(200)
                 ->assertJsonCount(3);  // ← SIN 'data'
    }

    /** @test */
    public function it_shows_only_own_loans_for_regular_users()
    {
        $user1 = Usuario::factory()->create(['rol' => 'usuario']);
        $user2 = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user1->createToken('Test Token')->accessToken;

        Prestamo::factory()->count(2)->create(['id_usuario' => $user1->id]);
        Prestamo::factory()->count(3)->create(['id_usuario' => $user2->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/loans');

        $response->assertStatus(200)
                 ->assertJsonCount(2);  // ← SIN 'data'
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/loans');

        $response->assertStatus(401);
    }
}