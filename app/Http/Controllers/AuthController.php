<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\RefreshToken;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

        // Create refresh token (rotating)
        $plainRefresh = Str::random(64);
        $refreshHash = Hash::make($plainRefresh);
        $expiresAt = Carbon::now()->addDays(30);

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $refreshHash,
            'expires_at' => $expiresAt,
        ]);

        // Set refresh token as HttpOnly cookie (for client compatibility)
        $cookie = cookie('refresh_token', $plainRefresh, 60 * 24 * 30, '/', null, false, true, false, 'None');

        $response = ApiResponse::success('LOGIN_SUCESSO', 'Login realizado com sucesso', [
            'token' => $token,
            'usuario' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'usuario' => $user->usuario,
            ],
        ]);

        return response()->json($response['body'], $response['statusCode'])->withCookie($cookie);

        Log::info('Login concluído com sucesso', [
            'usuario_id' => $user->id,
            'usuario' => $user->usuario,
            'ip' => $request->ip(),
        ]);

        return response()->json($response['body'], $response['statusCode']);
    }

    public function logout(Request $request): JsonResponse
    {
        // Try invalidate access token if provided
        try {
            if ($request->bearerToken()) {
                app('tymon.jwt')->setToken($request->bearerToken())->invalidate();
            }
        } catch (\Exception $e) {
            Log::warning('Falha ao invalidar access token no logout', ['error' => $e->getMessage()]);
        }

        // Revoke refresh token if provided in body OR in cookie
        $refresh = $request->input('refresh_token') ?? $request->cookie('refresh_token');
        if ($refresh) {
            try {
                $tokenRecord = RefreshToken::where('revoked', false)
                    ->where('expires_at', '>', Carbon::now())
                    ->get()
                    ->first(function ($r) use ($refresh) {
                        return Hash::check($refresh, $r->token_hash);
                    });

                if ($tokenRecord) {
                    $tokenRecord->revoked = true;
                    $tokenRecord->save();
                }
            } catch (\Exception $e) {
                Log::warning('Falha ao revogar refresh token no logout', ['error' => $e->getMessage()]);
            }
        }
        // Remove refresh cookie from client
        $forget = cookie()->forget('refresh_token');

        Log::info('Logout executado', [
            'usuario_id' => optional(auth('api')->user())->id,
            'ip' => $request->ip(),
        ]);
        $response = ApiResponse::success('LOGOUT_SUCESSO', 'Logout realizado com sucesso');

        return response()->json($response['body'], $response['statusCode'])->withCookie($forget);
    }

    public function refresh(Request $request): JsonResponse
    {
        $refresh = $request->input('refresh_token');

        if (!$refresh) {
            $response = ApiResponse::error('REFRESH_MISSING', 'Refresh token ausente', [], 400);
            return response()->json($response['body'], $response['statusCode']);
        }

        // Find matching refresh token (check cookie or body)
        $candidates = RefreshToken::where('revoked', false)->where('expires_at', '>', Carbon::now())->get();

        $found = null;
        foreach ($candidates as $candidate) {
            if (Hash::check($refresh, $candidate->token_hash)) {
                $found = $candidate;
                break;
            }
        }

        if (!$found) {
            $response = ApiResponse::error('REFRESH_INVALIDO', 'Refresh token inválido ou expirado', [], 401);
            return response()->json($response['body'], $response['statusCode']);
        }

        // Rotate: revoke old and create new
        $found->revoked = true;
        $found->save();

        $user = $found->user;
        if (!$user) {
            $response = ApiResponse::error('USUARIO_NAO_ENCONTRADO', 'Usuário do refresh token não encontrado', [], 404);
            return response()->json($response['body'], $response['statusCode']);
        }

        $newAccess = app('tymon.jwt.auth')->fromUser($user);
        $plainRefresh = Str::random(64);
        $refreshHash = Hash::make($plainRefresh);
        $expiresAt = Carbon::now()->addDays(30);

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $refreshHash,
            'expires_at' => $expiresAt,
        ]);

        // set new refresh token cookie and return new access token
        $cookie = cookie('refresh_token', $plainRefresh, 60 * 24 * 30, '/', null, false, true, false, 'None');

        $response = ApiResponse::success('REFRESH_SUCESSO', 'Tokens renovados com sucesso', [
            'token' => $newAccess,
        ]);

        return response()->json($response['body'], $response['statusCode'])->withCookie($cookie);
    }
}