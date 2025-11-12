<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies=[
        //'App\Models\Model' => 'App\Policies\ModelPolicy',

    ];

    public function boot():void
    {
         $this->registerPolicies();
         //if (! $this->app->routesAreCached()) {
             //Passport::routes();
         //}
        Passport::tokensExpireIn(now()->addHours(1));
        Passport::refreshTokensExpireIn(now()->addDays(7));
    }

}