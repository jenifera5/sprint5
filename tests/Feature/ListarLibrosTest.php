<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class ListarLibrosTest extends TestCase
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
    public function it_can_list_all_books()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

         Libro::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');  // ← AQUÍ
}

    /** @test */
    public function it_can_search_books_by_title()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        Libro::factory()->create(['titulo' => 'El Quijote', 'autor' => 'Cervantes']);
        Libro::factory()->create(['titulo' => 'Cien años de soledad', 'autor' => 'García Márquez']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/books?search=Quijote');

        $response->assertStatus(200)
                 ->assertJsonFragment(['titulo' => 'El Quijote']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(401);
    }
}