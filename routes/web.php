<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UsuarioController;
use App\Models\Sessao;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function (\Illuminate\Http\Request $request) {
    $logados = Sessao::where('active', true)
        ->with('user')
        ->orderBy('last_activity_at', 'desc')
        ->get()
        ->map(fn($s) => [
            'id' => $s->user_id,
            'nome' => $s->user?->nome ?? 'Desconhecido',
            'usuario' => $s->user?->usuario ?? '?',
            'ip' => $s->ip,
            'login' => $s->logged_in_at?->format('d/m/Y H:i:s'),
        ]);

    $disponiveis = User::where('ativo', true)->get()->map(fn($u) => [
        'id' => $u->id,
        'nome' => $u->nome,
        'usuario' => $u->usuario,
        'tipo' => $u->tipo_usuario,
        'senha' => $u->usuario === 'admin' ? 'admin123' : 'senha123',
    ]);

    $serverIps = array_filter([$_SERVER['SERVER_ADDR'] ?? null, gethostbyname(gethostname())]);

    return view()->file(__DIR__ . '/../resources/views/server-status.blade.php', [
        'logados' => $logados,
        'disponiveis' => $disponiveis,
        'meuIp' => $request->ip(),
        'serverIps' => array_values(array_unique($serverIps)),
    ]);
});

Route::get('/up', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'status' => 'sucesso',
        'mensagem' => 'Servidor online',
        'meu_ip' => $request->ip(),
        'server_ip' => $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()),
    ]);
});

Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::post('/usuarios/login', [AuthController::class, 'login']);

Route::post('/usuarios/logout', [AuthController::class, 'logout']);
Route::post('/token/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::get('/feed', [PostController::class, 'feed']);
    Route::get('/posts', [PostController::class, 'listAll']);
    Route::get('/usuarios-com-posts', [PostController::class, 'usersWithPosts']);

    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::get('/usuarios/logados', [AuthController::class, 'logados']);
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
});