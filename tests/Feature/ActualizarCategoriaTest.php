<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class ActualizarCategoriaTest extends TestCase
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
    public function it_can_update_a_category()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $categoria = Categoria::factory()->create(['nombre' => 'Viejo nombre']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/categories/{$categoria->id}", [
                             'nombre' => 'Nuevo nombre',
                             'descripcion' => 'Nueva descripción',
                         ]);

        $response->assertStatus(200)
         ->assertJson([
             'message' => 'Categoria actualizada correctamente',  // ← SIN TILDE
             'categoria' => ['nombre' => 'Nuevo nombre']
         ]);
        $this->assertDatabaseHas('categorias', ['nombre' => 'Nuevo nombre']);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $categoria = Categoria::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/categories/{$categoria->id}", [
                             'nombre' => 'Updated',
                             'descripcion' => 'Updated description',
                         ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $categoria = Categoria::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/categories/{$categoria->id}", []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'descripcion']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $categoria = Categoria::factory()->create();

        $response = $this->putJson("/api/categories/{$categoria->id}", [
            'nombre' => 'Test',
            'descripcion' => 'Test',
        ]);

        $response->assertStatus(401);
    }
}