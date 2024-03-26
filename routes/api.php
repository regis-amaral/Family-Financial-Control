<?php


use App\Http\Controllers\FinancialServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// GRUPO DE ROTAS NÃO AUTENTICADAS
Route::middleware('guest')->group(function () {
    // Oculta qualquer informação de rota se não estiver autenticado
    Route::fallback(function () {
        abort(404);
    });
    // Cadastro de Usuário
    Route::post('register', [RegisterController::class, 'register']);
    // Login de usuário
    Route::post('login', [LoginController::class, 'login'])->middleware(['throttle:login']);
});

// GRUPO DE ROTAS AUTENTICADAS
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::resource('financial-service', FinancialServiceController::class);
});

