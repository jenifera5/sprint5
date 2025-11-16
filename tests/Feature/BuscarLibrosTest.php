<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class BuscarLibrosTest extends TestCase
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
    public function it_can_search_books_by_title()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        Libro::factory()->create(['titulo' => 'El Quijote', 'autor' => 'Cervantes']);
        Libro::factory()->create(['titulo' => 'Cien años de soledad', 'autor' => 'García Márquez']);
        Libro::factory()->create(['titulo' => 'Don Juan', 'autor' => 'Molière']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/books/search?query=Quijote');

        $response->assertStatus(200)
                 ->assertJsonFragment(['titulo' => 'El Quijote'])
                 ->assertJsonMissing(['titulo' => 'Don Juan']);
    }

    /** @test */
    public function it_can_search_books_by_author()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        Libro::factory()->create(['titulo' => 'El Quijote', 'autor' => 'Cervantes']);
        Libro::factory()->create(['titulo' => 'Cien años de soledad', 'autor' => 'García Márquez']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/books/search?query=Cervantes');

        $response->assertStatus(200)
                 ->assertJsonFragment(['autor' => 'Cervantes']);
    }

    /** @test */
public function it_returns_empty_if_no_results()
{
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        Libro::factory()->create(['titulo' => 'El Quijote', 'autor' => 'Cervantes']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/books/search?query=NoExiste');

        $response->assertStatus(200)
             ->assertJsonCount(0, 'data');  // ← AÑADIR 'data'
}
    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/books/search?query=test');

        $response->assertStatus(401);
    }
}