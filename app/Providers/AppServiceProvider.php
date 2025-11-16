<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       // Evita que Passport migre sus tablas en testing o cuando ya existen
        if (env('PASSPORT_SKIP_MIGRATIONS', false)) {
            if (method_exists(Passport::class, 'ignoreMigrations')) {
                Passport::ignoreMigrations();
            }
        }
    }
}

