<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class CrearCategoriaTest extends TestCase
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
    public function it_can_create_a_category()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/categories', [
                             'nombre' => 'Ciencia Ficción',
                             'descripcion' => 'Libros de ciencia ficción',  // ← AÑADIDO
                         ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Categoria creada correctamente',
                     'categoria' => ['nombre' => 'Ciencia Ficción']
                 ]);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Ciencia Ficción']);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/categories', [
                             'nombre' => 'Test Category',
                             'descripcion' => 'Test description',  // ← AÑADIDO
                         ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/categories', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'descripcion']);  // ← AÑADIDO 'descripcion'
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/categories', [
            'nombre' => 'Test',
            'descripcion' => 'Test',  // ← AÑADIDO
        ]);

        $response->assertStatus(401);
    }
}