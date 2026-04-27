<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtualizacaoUsuarioRequest;
use App\Http\Requests\CadastroRequest;
use App\Models\User;
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

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_CRIADO',
            'mensagem' => 'Usuario cadastrado com sucesso',
            'dados' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'usuario' => $user->usuario,
                'email' => $user->email,
                'biografia' => $user->biografia,
                'foto_url' => $user->foto_url,
            ],
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'USUARIO_NAO_ENCONTRADO',
                'mensagem' => 'Usuario nao encontrado',
                'dados' => (object) [],
            ], 404);
        }

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_ENCONTRADO',
            'mensagem' => 'Dados do usuario recuperados',
            'dados' => [
                'id' => (string) $user->id,
                'nome_completo' => $user->nome,
                'usuario' => $user->usuario,
                'email' => $user->email,
                'biografia' => $user->biografia,
                'foto_url' => $user->foto_url,
            ],
        ], 200);
    }

    public function update(AtualizacaoUsuarioRequest $request, string $id): JsonResponse
    {
        $user = User::query()->where('ativo', true)->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'USUARIO_NAO_ENCONTRADO',
                'mensagem' => 'Usuario nao encontrado',
                'dados' => (object) [],
            ], 404);
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

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_ATUALIZADO',
            'mensagem' => 'Usuario atualizado com sucesso',
            'dados' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'usuario' => $user->usuario,
                'email' => $user->email,
            ],
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::query()->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'USUARIO_NAO_ENCONTRADO',
                'mensagem' => 'Usuario nao encontrado',
                'dados' => (object) [],
            ], 404);
        }

        $user->ativo = false;
        $user->save();
        $user->delete();

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_REMOVIDO',
            'mensagem' => 'Usuario removido com sucesso',
            'dados' => (object) [],
        ], 200);
    }
}