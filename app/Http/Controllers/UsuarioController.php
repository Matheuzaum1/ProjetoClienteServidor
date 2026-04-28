<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtualizacaoUsuarioRequest;
use App\Http\Requests\CadastroRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function store(CadastroRequest $request): JsonResponse
    {
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

        return response()->json($response['body'], $response['statusCode']);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($id);

        if (!$user) {
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

        return response()->json($response['body'], $response['statusCode']);
    }

    public function update(AtualizacaoUsuarioRequest $request, string $id): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($id);

        if (!$user) {
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
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário não encontrado', [], 404);

            return response()->json($response['body'], $response['statusCode']);
        }

        $user->ativo = false;
        $user->save();
        $user->delete();

        $response = ApiResponse::success('USUARIO_REMOVIDO', 'Usuário removido com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }
}