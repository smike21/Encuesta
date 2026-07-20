<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Railway termina TLS en su proxy y reenvía la petición a PHP por HTTP.
        // Forzamos los enlaces y formularios públicos a conservar HTTPS.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
