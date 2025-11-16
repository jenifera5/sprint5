<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\Prestamo;
use Laravel\Passport\ClientRepository;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🔹 1. Inicializar Passport
        $this->call(\Database\Seeders\PassportClientSeeder::class);

        $alreadyExists = DB::table('oauth_personal_access_clients')->exists();
        if (! $alreadyExists) {
            $repo = new ClientRepository();
            $repo->createPersonalAccessClient(
                userId: null,
                name:   'Personal Access Client',
                redirect: 'http://localhost'
            );
        }

       $this->call([
            PassportClientSeeder::class,
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            LibroSeeder::class,
            PrestamoSeeder::class,
        ]);
    }
}
