<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialServiceController;
use App\Http\Controllers\RegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ROTAS NÃO AUTENTICADAS
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

// ROTAS AUTENTICADAS
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('financial-service', FinancialServiceController::class);
});

