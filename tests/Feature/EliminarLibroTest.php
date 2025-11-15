<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class EliminarLibroTest extends TestCase
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
    public function it_can_delete_a_book()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/books/{$libro->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Libro eliminado correctamente']);

        $this->assertDatabaseMissing('libros', ['id' => $libro->id]);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/books/{$libro->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_404_if_book_not_found()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/books/99999");

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Libro no encontrado']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $libro = Libro::factory()->create();

        $response = $this->deleteJson("/api/books/{$libro->id}");

        $response->assertStatus(401);
    }
}