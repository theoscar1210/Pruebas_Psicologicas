<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Portal candidato: 5 intentos por minuto por IP
        // Código de 8 chars — sin esto es vulnerable a fuerza bruta
        RateLimiter::for('candidate-access', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Verificación y activación 2FA: 5 intentos cada 5 minutos por IP
        // TOTP de 6 dígitos = 1M combinaciones, debe ser throttleado
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinutes(5, 5)->by($request->ip());
        });
    }
}
