<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('usuario', $request->string('usuario'))
            ->where('ativo', true)
            ->first();

        if (!$user || !Hash::check($request->string('senha'), $user->password)) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'CREDENCIAIS_INVALIDAS',
                'mensagem' => 'Usuario ou senha invalidos',
                'dados' => (object) [],
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'LOGIN_SUCESSO',
            'mensagem' => 'Login realizado com sucesso',
            'dados' => [
                'token' => $token,
                'usuario' => [
                    'id' => (string) $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'usuario' => $user->usuario,
                ],
            ],
        ], 200);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'LOGOUT_SUCESSO',
            'mensagem' => 'Logout realizado com sucesso',
            'dados' => (object) [],
        ], 200);
    }
}