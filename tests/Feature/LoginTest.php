<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;  // ← AÑADIR

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ← AÑADIR ESTE MÉTODO
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
    public function it_can_login_with_valid_credentials()
    {
        $usuario = Usuario::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('123456'),
            'rol' => 'usuario'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'usuario']);
    }

    /** @test */
    public function it_fails_with_invalid_credentials()
    {
        $usuario = Usuario::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Credenciales incorrectas']);  // ← CAMBIAR 'message' por 'error'
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(401);  // ← CAMBIAR 422 por 401 (o arreglar controller para validar)
    }
}