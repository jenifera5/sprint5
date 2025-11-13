<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\DB;

class PassportClientSeeder extends Seeder
{
    public function run(): void
    {
        if (!DB::table('oauth_personal_access_clients')->exists()) {
            $repo = new ClientRepository();
            $repo->createPersonalAccessClient(
                userId: null,
                name: 'Personal Access Client',
                redirect: 'http://localhost'
            );
        }
    }
}
