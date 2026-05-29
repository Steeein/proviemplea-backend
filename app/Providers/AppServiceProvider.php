<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    // Ignorar advertencia de swagger-php que Laravel convierte en excepción
    set_error_handler(function (int $errno, string $errstr) {
        if (str_contains($errstr, 'Required @OA\PathItem() not found')) {
            return true;
        }
        return false;
    }, E_USER_WARNING | E_USER_NOTICE);

    // Rate Limiting: 60 solicitudes por minuto por IP para todos los endpoints API.
    // Configurable vía env RATE_LIMIT_PER_MINUTE.
    RateLimiter::for('api', function (Request $request) {
        $limit = (int) env('RATE_LIMIT_PER_MINUTE', 60);
        return Limit::perMinute($limit)->by($request->ip());
    });
}
}
