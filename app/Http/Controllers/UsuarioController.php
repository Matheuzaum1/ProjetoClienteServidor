<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtualizacaoUsuarioRequest;
use App\Http\Requests\CadastroRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
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
            'ip' => request()->ip(),
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function update(AtualizacaoUsuarioRequest $request, string $id): JsonResponse
    {
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
            'ip' => request()->ip(),
        ]);

        $response = ApiResponse::success('USUARIO_REMOVIDO', 'Usuário removido com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }
}