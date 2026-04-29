<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        Log::info('Login solicitado', [
            'usuario' => $request->string('usuario'),
            'ip' => $request->ip(),
        ]);

        $user = User::query()
            ->where('usuario', $request->string('usuario'))
            ->where('ativo', true)
            ->first();

        if (!$user || !Hash::check($request->string('senha'), $user->password)) {
            Log::warning('Login recusado por credenciais inválidas', [
                'usuario' => $request->string('usuario'),
                'ip' => $request->ip(),
            ]);

            $response = ApiResponse::error('CREDENCIAIS_INVALIDAS', 'Usuário ou senha inválidos', [], 401);

            return response()->json($response['body'], $response['statusCode']);
        }

        $token = app('tymon.jwt.auth')->fromUser($user);

        $response = ApiResponse::success('LOGIN_SUCESSO', 'Login realizado com sucesso', [
            'token' => $token,
            'usuario' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'usuario' => $user->usuario,
            ],
        ]);

        Log::info('Login concluído com sucesso', [
            'usuario_id' => $user->id,
            'usuario' => $user->usuario,
            'ip' => $request->ip(),
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->bearerToken()) {
            app('tymon.jwt')->setToken($request->bearerToken())->invalidate();
        }

        Log::info('Logout executado', [
            'usuario_id' => optional(auth('api')->user())->id,
            'ip' => $request->ip(),
        ]);

        $response = ApiResponse::success('LOGOUT_SUCESSO', 'Logout realizado com sucesso');

        return response()->json($response['body'], $response['statusCode']);
    }
}