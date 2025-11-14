<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente personal para Passport
        $repo = new ClientRepository();
        $repo->createPersonalAccessClient(
            null,
            'Personal Access Client',
            'http://localhost'
        );
    }

    /** @test */
    public function it_can_logout_authenticated_user()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        
        // Crear token REAL en lugar de mock
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Sesión cerrada correctamente']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}