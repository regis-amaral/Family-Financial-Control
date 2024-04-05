<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FinancialControl\FinancialServiceController;
use App\Http\Controllers\FinancialControl\FinancialTransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\FinancialService;
use App\Models\FinancialTransaction;

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

    // ROTAS PARA FINANCIAL SERVICE CONTROLLER
    Route::controller(FinancialServiceController::class)->group(function () {
        Route::get('financial/services', 'index')->can('viewAny', FinancialService::class);
        Route::post('financial/services', 'store')->can('create', FinancialService::class);
        Route::get('financial/services/{financial_service}', 'show')->can('view', 'financial_service');
        Route::put('financial/services/{financial_service}', 'update')->can('update', 'financial_service');
        Route::delete('financial/services/{financial_service}', 'destroy')->can('delete', 'financial_service');
    });

    // ROTAS PARA FINANCIAL TRANSACTION CONTROLLER
    Route::controller(FinancialTransactionController::class)->middleware('can:view,financial_service')->group(function () {
        Route::get('financial/services/{financial_service}/transactions', 'index')->can('viewAny', FinancialService::class);
        Route::post('financial/services/{financial_service}/transactions', 'store')->can('create', FinancialTransaction::class);
        Route::get('financial/services/{financial_service}/transactions/{financial_transaction}', 'show')->can('view', 'financial_transaction');
        Route::put('financial/services/{financial_service}/transactions/{financial_transaction}', 'update')->can('update', 'financial_transaction');
        Route::delete('financial/services/{financial_service}/transactions/{financial_transaction}', 'destroy')->can('delete', 'financial_transaction');
    });
});

