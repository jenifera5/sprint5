<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Prestamo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class LibrosPopularesTest extends TestCase
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
    public function it_can_get_most_borrowed_books()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        // Crear libros
        $libro1 = Libro::factory()->create(['titulo' => 'Libro Popular']);
        $libro2 = Libro::factory()->create(['titulo' => 'Libro Medio']);
        $libro3 = Libro::factory()->create(['titulo' => 'Libro Poco Popular']);

        // Crear préstamos usando el nombre correcto de columna
        Prestamo::factory()->count(5)->create(['id_libro' => $libro1->id]);  // ← CAMBIAR
        Prestamo::factory()->count(3)->create(['id_libro' => $libro2->id]);
        Prestamo::factory()->count(1)->create(['id_libro' => $libro3->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/books/stats/popular');  // ← CAMBIAR URL

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'titulo', 'autor', 'prestamos_count']
                     ]
                 ]);

        // El primer libro debe ser el más prestado
        $data = $response->json('data');
        $this->assertEquals('Libro Popular', $data[0]['titulo']);
        $this->assertEquals(5, $data[0]['prestamos_count']);
    }

    /** @test */
    public function it_returns_empty_if_no_books()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/books/stats/popular');  // ← CAMBIAR URL

        $response->assertStatus(200)
                 ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/books/stats/popular');  // ← CAMBIAR URL

        $response->assertStatus(401);
    }
}