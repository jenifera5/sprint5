<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class ListarCategoriasTest extends TestCase
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
    public function it_can_list_all_categories()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        Categoria::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3);  // ← SIN 'data'
    }

    /** @test */
    public function it_returns_empty_if_no_categories()
    {
        $user = Usuario::factory()->create(['rol' => 'usuario']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(0);  // ← SIN 'data'
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(401);
    }
}