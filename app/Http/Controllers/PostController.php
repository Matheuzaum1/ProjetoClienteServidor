<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Curtida;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(string $idUsuario): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $posts = Post::query()
            ->where('user_id', $idUsuario)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) {
                return [
                    'id' => (string) $post->id,
                    'legenda' => $post->legenda,
                    'imagem' => $post->imagem,
                    'curtidas' => (string) Curtida::where('post_id', $post->id)->count(),
                ];
            });

        Log::info('Listagem de posts realizada', [
            'usuario_id' => $idUsuario,
            'requisitante_id' => Auth::id(),
            'total_posts' => $posts->count(),
            'ip' => request()->ip(),
        ]);

        if ($posts->isEmpty()) {
            $response = ApiResponse::error('NENHUM_POST_ENCONTRADO', 'Nenhum post encontrado para este usuário', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $body = [
            'status' => 'sucesso',
            'codigo' => 'LISTAGEM_POSTS_SUCESSO',
            'mensagem' => 'Posts listados com sucesso',
            'posts' => $posts,
        ];

        return response()->json($body, 200);
    }

    public function store(Request $request, string $idUsuario): JsonResponse
    {
        if (Auth::id() != $idUsuario) {
            $response = ApiResponse::error('ACESSO_NEGADO', 'Você só pode criar posts para seu próprio usuário', [], 403);
            return response()->json($response['body'], $response['statusCode']);
        }

        $validated = $request->validate([
            'imagem' => 'required|string',
            'legenda' => 'required|string|min:5|max:200',
        ]);

        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post = Post::create([
            'user_id' => $idUsuario,
            'imagem' => $validated['imagem'],
            'legenda' => $validated['legenda'],
        ]);

        Log::info('Post criado', [
            'post_id' => $post->id,
            'usuario_id' => $idUsuario,
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('POST_CRIADO', 'Post criado com sucesso', [
            'post' => [
                'id' => (string) $post->id,
                'imagem' => $post->imagem,
                'legenda' => $post->legenda,
                'curtidas' => '0',
            ],
        ], 201);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function show(string $idUsuario, string $idPost): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post = Post::query()->where('user_id', $idUsuario)->find($idPost);

        if (!$post) {
            $response = ApiResponse::error('POST_NAO_ENCONTRADO', 'Post não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        Log::info('Consulta de post realizada', [
            'post_id' => $post->id,
            'usuario_id' => $idUsuario,
            'requisitante_id' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        $body = [
            'status' => 'sucesso',
            'codigo' => 'POST_ENCONTRADO',
            'mensagem' => 'Post encontrado com sucesso',
            'dados' => [
                'id' => (string) $post->id,
                'legenda' => $post->legenda,
                'imagem' => $post->imagem,
                'curtidas' => (string) Curtida::where('post_id', $post->id)->count(),
            ],
        ];

        return response()->json($body, 200);
    }

    public function update(Request $request, string $idUsuario, string $idPost): JsonResponse
    {
        if (Auth::id() != $idUsuario) {
            $response = ApiResponse::error('ACESSO_NEGADO', 'Você não tem permissão para atualizar este post', [], 403);
            return response()->json($response['body'], $response['statusCode']);
        }

        $validated = $request->validate([
            'legenda' => 'required|string|min:5|max:200',
        ]);

        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post = Post::query()->where('user_id', $idUsuario)->find($idPost);

        if (!$post) {
            $response = ApiResponse::error('POST_NAO_ENCONTRADO', 'Post não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post->update([
            'legenda' => $validated['legenda'],
        ]);

        Log::info('Post atualizado', [
            'post_id' => $post->id,
            'usuario_id' => $idUsuario,
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('POST_ATUALIZADO', 'Post atualizado com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }

    public function destroy(string $idUsuario, string $idPost): JsonResponse
    {
        if (Auth::id() != $idUsuario) {
            $response = ApiResponse::error('ACESSO_NEGADO', 'Você não tem permissão para deletar este post', [], 403);
            return response()->json($response['body'], $response['statusCode']);
        }

        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post = Post::query()->where('user_id', $idUsuario)->find($idPost);

        if (!$post) {
            $response = ApiResponse::error('POST_NAO_ENCONTRADO', 'Post não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post->delete();

        Log::info('Post deletado', [
            'post_id' => $idPost,
            'usuario_id' => $idUsuario,
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('POST_REMOVIDO', 'Post removido com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }

    public function curtir(string $idUsuario, string $idPost): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post = Post::query()->where('user_id', $idUsuario)->find($idPost);

        if (!$post) {
            $response = ApiResponse::error('POST_NAO_ENCONTRADO', 'Post não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $existing = Curtida::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $response = ApiResponse::error('CURTIDA_EXISTENTE', 'Você já curtiu este post', [], 400);
            return response()->json($response['body'], $response['statusCode']);
        }

        Curtida::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);

        $total = Curtida::where('post_id', $post->id)->count();
        $post->update(['curtidas' => $total]);

        Log::info('Post curtido', [
            'post_id' => $post->id,
            'usuario_id_autor' => $idUsuario,
            'usuario_id_curtiu' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('CURTIDA_ADICIONADA', 'Curtida adicionada com sucesso', [], 201);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function listAll(): JsonResponse
    {
        $posts = Post::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) {
                $user = $post->user;
                return [
                    'id' => (string) $post->id,
                    'legenda' => $post->legenda,
                    'imagem' => $post->imagem,
                    'curtidas' => (string) Curtida::where('post_id', $post->id)->count(),
                    'usuario' => [
                        'id' => (string) $user->id,
                        'nome' => $user->nome,
                        'usuario' => $user->usuario,
                    ],
                    'created_at' => $post->created_at?->toISOString(),
                ];
            });

        $body = [
            'status' => 'sucesso',
            'codigo' => 'LISTAGEM_TODOS_POSTS_SUCESSO',
            'mensagem' => 'Posts listados com sucesso',
            'posts' => $posts,
        ];

        return response()->json($body, 200);
    }

    public function usersWithPosts(): JsonResponse
    {
        $userIds = Post::query()
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        $users = User::query()
            ->whereIn('id', $userIds)
            ->where('ativo', true)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => (string) $user->id,
                    'nome' => $user->nome,
                    'usuario' => $user->usuario,
                    'biografia' => $user->biografia,
                    'foto_url' => $user->foto_url,
                ];
            });

        $body = [
            'status' => 'sucesso',
            'codigo' => 'USUARIOS_COM_POSTS',
            'mensagem' => 'Usuários com posts listados com sucesso',
            'usuarios' => $users,
        ];

        return response()->json($body, 200);
    }

    public function feed(): JsonResponse
    {
        $posts = Post::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) {
                $user = $post->user;
                return [
                    'id' => (string) $post->id,
                    'legenda' => $post->legenda,
                    'imagem' => $post->imagem,
                    'curtidas' => (string) Curtida::where('post_id', $post->id)->count(),
                    'usuario' => [
                        'id' => (string) $user->id,
                        'nome' => $user->nome,
                        'usuario' => $user->usuario,
                        'foto' => $user->foto_url,
                    ],
                    'created_at' => $post->created_at?->toISOString(),
                ];
            });

        $body = [
            'status' => 'sucesso',
            'codigo' => 'FEED_CARREGADO',
            'mensagem' => 'Feed carregado com sucesso',
            'posts' => $posts,
        ];

        return response()->json($body, 200);
    }

    public function descurtir(string $idUsuario, string $idPost): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($idUsuario);

        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $post = Post::query()->where('user_id', $idUsuario)->find($idPost);

        if (!$post) {
            $response = ApiResponse::error('POST_NAO_ENCONTRADO', 'Post não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $existing = Curtida::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if (!$existing) {
            $response = ApiResponse::error('CURTIDA_INEXISTENTE', 'Você não curtiu este post', [], 400);
            return response()->json($response['body'], $response['statusCode']);
        }

        $existing->delete();

        $total = Curtida::where('post_id', $post->id)->count();
        $post->update(['curtidas' => $total]);

        Log::info('Curtida removida', [
            'post_id' => $post->id,
            'usuario_id_autor' => $idUsuario,
            'usuario_id_descurtiu' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('CURTIDA_REMOVIDA', 'Curtida removida com sucesso', [], 200);

        return response()->json($response['body'], $response['statusCode']);
    }

}
