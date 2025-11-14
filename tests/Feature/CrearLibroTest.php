<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class CrearLibroTest extends TestCase
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
    public function it_can_create_a_book()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/books', [
                             'titulo' => 'Dune',
                             'autor' => 'Frank Herbert',
                             'anio' => 1965,
                             'disponibles' => 10,
                         ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Libro creado correctamente',
                     'libro' => ['titulo' => 'Dune']
                 ]);

        $this->assertDatabaseHas('libros', ['titulo' => 'Dune']);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/books', [
                             'titulo' => 'Test Book',
                             'autor' => 'Test Author',
                             'anio' => 2024,
                             'disponibles' => 5,
                         ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/books', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['titulo', 'autor', 'anio', 'disponibles']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/books', [
            'titulo' => 'Test',
            'autor' => 'Author',
            'anio' => 2024,
            'disponibles' => 1,
        ]);

        $response->assertStatus(401);
    }
}