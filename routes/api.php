<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::post('/usuarios/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/usuarios/logout', [AuthController::class, 'logout']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
});