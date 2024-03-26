<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        // ########### LIMITAÇÕES DE TAXAS ###########

        // TENTATIVAS DE LOGIN
        RateLimiter::for('login', function (Request $request) {
            return [
                // 3 por minuto para um email específico
                Limit::perMinute(3)->response(function (Request $request, array $headers)  {
                    return response([
                        'success' => false,
                        'message' => "Três tentativas incorretas. Aguarde 60 segundos.",
                    ], 429, $headers);
                })->by($request->input('email')),
                // 500 por minuto para um ip específico
                Limit::perMinute(500)->response(function (Request $request, array $headers)  {
                    return response([
                        'success' => false,
                        'message' => "Múltiplas solicitações. Aguarde 60 segundos.",
                    ], 429, $headers);
                })->by($request->ip())
            ];
        });

        // ########### FIM ###########


    }
}
