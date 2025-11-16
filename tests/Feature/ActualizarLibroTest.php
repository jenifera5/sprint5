<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class ActualizarLibroTest extends TestCase
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
    public function it_can_update_a_book()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create(['titulo' => 'Viejo título']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/books/{$libro->id}", [
                             'titulo' => 'Nuevo título',
                             'autor' => 'Nuevo autor',
                             'anio' => 2020,
                             'disponibles' => 5,
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Libro actualizado correctamente',
                     'libro' => ['titulo' => 'Nuevo título']
                 ]);

        $this->assertDatabaseHas('libros', ['titulo' => 'Nuevo título']);
    }

    /** @test */
    public function it_requires_admin_role()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/books/{$libro->id}", [
                             'titulo' => 'Updated',
                             'autor' => 'Author',
                             'anio' => 2024,
                             'disponibles' => 1,
                         ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $admin = Usuario::factory()->create(['rol' => 'admin']);
        $token = $admin->createToken('Test Token')->accessToken;

        $libro = Libro::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/books/{$libro->id}", []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['titulo', 'autor', 'anio', 'disponibles']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $libro = Libro::factory()->create();

        $response = $this->putJson("/api/books/{$libro->id}", [
            'titulo' => 'Test',
            'autor' => 'Author',
            'anio' => 2024,
            'disponibles' => 1,
        ]);

        $response->assertStatus(401);
    }
}