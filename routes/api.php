<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialServiceController;
use App\Http\Controllers\RegisterController;

// SEM AUTENTICAÇÃO = SEM INFORMAÇÃO
Route::middleware('guest')->group(function () {
    Route::fallback(function () {
        abort(404);
    });
});

// ROTAS NÃO AUTENTICADAS
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

// ROTAS AUTENTICADAS
Route::middleware('auth:sanctum')->group( function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::resource('financial-service', FinancialServiceController::class);
});

