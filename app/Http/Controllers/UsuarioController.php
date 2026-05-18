<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtualizacaoUsuarioRequest;
use App\Http\Requests\CadastroRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    private function isUserAdm(): bool
    {
        return Auth::check() && Auth::user()->tipo_usuario === 'adm';
    }

    private function canAccessUser(string $userId): bool
    {
        $user = Auth::user();

        return $user && ($user->id == $userId || $this->isUserAdm());
    }

    public function index(): JsonResponse
    {
        if (!$this->isUserAdm()) {
            Log::warning('Tentativa de listagem de usuários por usuário comum', [
                'usuario_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('ACESSO_NEGADO', 'Apenas administradores podem listar usuários', [], 403);

            return response()->json($response['body'], $response['statusCode']);
        }

        $usuarios = User::query()->where('ativo', true)->get()->map(function ($user) {
            return [
                'id' => (string) $user->id,
                'nome_completo' => $user->nome,
                'usuario' => $user->usuario,
                'email' => $user->email,
                'biografia' => $user->biografia,
                'foto_url' => $user->foto_url,
                'tipo_usuario' => $user->tipo_usuario,
            ];
        });

        Log::info('Listagem de usuários realizada', [
            'usuario_adm_id' => Auth::id(),
            'total_usuarios' => $usuarios->count(),
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('LISTAGEM_SUCESSO', 'Usuários listados com sucesso', [
            'usuarios' => $usuarios,
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function store(CadastroRequest $request): JsonResponse
    {
        Log::info('Cadastro solicitado', [
            'usuario' => $request->string('usuario'),
            'email' => $request->string('email'),
            'ip' => $request->ip(),
        ]);

        $user = User::create([
            'nome' => $request->string('nome'),
            'usuario' => $request->string('usuario'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('senha')),
            'biografia' => $request->input('biografia'),
            'foto_url' => $request->input('foto'),
            'ativo' => true,
            'tipo_usuario' => 'comum',
        ]);

        $response = ApiResponse::success('USUARIO_CRIADO', 'Usuário cadastrado com sucesso', [
            'id' => (string) $user->id,
            'nome' => $user->nome,
            'usuario' => $user->usuario,
            'email' => $user->email,
            'biografia' => $user->biografia,
            'foto_url' => $user->foto_url,
        ], 201);

        Log::info('Cadastro concluído', [
            'usuario_id' => $user->id,
            'usuario' => $user->usuario,
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function show(string $id): JsonResponse
    {
        if (!$this->canAccessUser($id)) {
            Log::warning('Tentativa de acesso a usuário não autorizado', [
                'usuario_id_requerido' => $id,
                'usuario_id_autenticado' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('ACESSO_NEGADO', 'Você não tem permissão para acessar este usuário', [], 403);

            return response()->json($response['body'], $response['statusCode']);
        }

        $user = User::query()->where('ativo', true)->find($id);

        if (!$user) {
            Log::warning('Consulta de usuário não encontrado', [
                'usuario_id' => $id,
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);

            return response()->json($response['body'], $response['statusCode']);
        }

        $response = ApiResponse::success('USUARIO_ENCONTRADO', 'Dados do usuário recuperados', [
            'id' => (string) $user->id,
            'nome_completo' => $user->nome,
            'usuario' => $user->usuario,
            'email' => $user->email,
            'biografia' => $user->biografia,
            'foto_url' => $user->foto_url,
        ]);

        Log::info('Consulta de usuário realizada', [
            'usuario_id' => $user->id,
            'usuario_autenticado' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function update(AtualizacaoUsuarioRequest $request, string $id): JsonResponse
    {
        if (!$this->canAccessUser($id)) {
            Log::warning('Tentativa de atualização não autorizada', [
                'usuario_id_requerido' => $id,
                'usuario_id_autenticado' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('ACESSO_NEGADO', 'Você não tem permissão para atualizar este usuário', [], 403);

            return response()->json($response['body'], $response['statusCode']);
        }

        $user = User::query()->where('ativo', true)->find($id);

        if (!$user) {
            Log::warning('Atualização para usuário não encontrado', [
                'usuario_id' => $id,
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);

            return response()->json($response['body'], $response['statusCode']);
        }

        $payload = [
            'nome' => $request->string('nome'),
            'usuario' => $request->string('usuario'),
            'email' => $request->string('email'),
            'biografia' => $request->input('biografia'),
            'foto_url' => $request->input('foto'),
        ];

        if ($request->filled('senha')) {
            $payload['password'] = Hash::make($request->string('senha'));
        }

        $user->update($payload);

        Log::info('Usuário atualizado', [
            'usuario_id' => $user->id,
            'usuario' => $user->usuario,
            'usuario_autenticado' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('USUARIO_ATUALIZADO', 'Usuário atualizado com sucesso', [
            'id' => (string) $user->id,
            'nome' => $user->nome,
            'usuario' => $user->usuario,
            'email' => $user->email,
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function destroy(string $id): JsonResponse
    {
        if (!$this->canAccessUser($id)) {
            Log::warning('Tentativa de exclusão não autorizada', [
                'usuario_id_requerido' => $id,
                'usuario_id_autenticado' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('ACESSO_NEGADO', 'Você não tem permissão para deletar este usuário', [], 403);

            return response()->json($response['body'], $response['statusCode']);
        }

        $user = User::query()->find($id);

        if (!$user) {
            Log::warning('Exclusão para usuário não encontrado', [
                'usuario_id' => $id,
                'ip' => request()->ip(),
            ]);

            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);

            return response()->json($response['body'], $response['statusCode']);
        }

        $user->ativo = false;
        $user->save();
        $user->delete();

        Log::info('Usuário desativado/removido', [
            'usuario_id' => $user->id,
            'usuario_removido_por' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('USUARIO_REMOVIDO', 'Usuário removido com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }
}