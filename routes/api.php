<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FinancialControl\FinancialServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\FinancialService;

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
        return new \App\Http\Resources\Auth\UserResource($request->user());
    });

    // ROTAS PARA FINANCIAL SERVICE
    Route::get('financial-service', [FinancialServiceController::class, 'index'])->can('viewAny',FinancialService::class);
    Route::post('financial-service', [FinancialServiceController::class, 'store'])->can('create',FinancialService::class);
    Route::get('financial-service/{financial_service}', [FinancialServiceController::class, 'show'])->can('view','financial_service');
    Route::put('financial-service/{financial_service}', [FinancialServiceController::class, 'update'])->can('update','financial_service');
    Route::delete('financial-service/{financial_service}', [FinancialServiceController::class, 'destroy'])->can('delete','financial_service');

});

