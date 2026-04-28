<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Support\ApiResponse;
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
            $response = ApiResponse::error('CREDENCIAIS_INVALIDAS', 'Usuário ou senha inválidos', [], 401);

            return response()->json($response['body'], $response['statusCode']);
        }

        $token = JWTAuth::fromUser($user);

        $response = ApiResponse::success('LOGIN_SUCESSO', 'Login realizado com sucesso', [
            'token' => $token,
            'usuario' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'usuario' => $user->usuario,
            ],
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        $response = ApiResponse::success('LOGOUT_SUCESSO', 'Logout realizado com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }
}