<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::post('/usuarios/login', [AuthController::class, 'login']);
Route::post('/usuarios/logout', [AuthController::class, 'logout']);
Route::post('/token/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::get('/feed', [PostController::class, 'feed']);
    Route::get('/posts', [PostController::class, 'listAll']);
    Route::get('/usuarios-com-posts', [PostController::class, 'usersWithPosts']);

    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    Route::get('/usuarios/{idUsuario}/posts', [PostController::class, 'index']);
    Route::post('/usuarios/{idUsuario}/posts', [PostController::class, 'store']);
    Route::get('/usuarios/{idUsuario}/posts/{idPost}', [PostController::class, 'show']);
    Route::post('/usuarios/{idUsuario}/posts/{idPost}', [PostController::class, 'curtir']);
    Route::delete('/usuarios/{idUsuario}/posts/{idPost}/curtir', [PostController::class, 'descurtir']);
    Route::patch('/usuarios/{idUsuario}/posts/{idPost}', [PostController::class, 'update']);
    Route::delete('/usuarios/{idUsuario}/posts/{idPost}', [PostController::class, 'destroy']);

    Route::get('/usuarios/logados', [AuthController::class, 'logados']);
});