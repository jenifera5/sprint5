<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente personal para Passport
        $repo = new ClientRepository();
        $repo->createPersonalAccessClient(
            null,
            'Personal Access Client',
            'http://localhost'
        );
    }

    /** @test */
    public function it_can_register_a_user()
    {
        $response = $this->postJson('/api/register', [
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
           
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'usuario' => ['id', 'nombre', 'email', 'rol'],
                     'token'
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'email', 'password']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $response = $this->postJson('/api/register', [
            'nombre' => 'Test User',
            'email' => 'invalid-email',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_validates_password_confirmation()
    {
        $response = $this->postJson('/api/register', [
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_prevents_duplicate_emails()
    {
        Usuario::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'nombre' => 'Test User',
            'email' => 'existing@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}