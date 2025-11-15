<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class EliminarCategoriaTest extends TestCase
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
    public function it_can_delete_a_category()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $categoria = Categoria::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/categories/{$categoria->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Categoria eliminada correctamente']);

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $categoria = Categoria::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/categories/{$categoria->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_404_if_category_not_found()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/categories/99999");

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Categoria no encontrada']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $categoria = Categoria::factory()->create();

        $response = $this->deleteJson("/api/categories/{$categoria->id}");

        $response->assertStatus(401);
    }
}