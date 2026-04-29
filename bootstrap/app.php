<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Support\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'usuarios',
            'usuarios/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                $response = ApiResponse::error('NAO_AUTENTICADO', 'Não autenticado.', [], 401);

                return response()->json($response['body'], $response['statusCode']);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                $response = ApiResponse::error('VALIDACAO_FALHOU', 'Dados de entrada inválidos.', [
                    'erros' => $e->errors(),
                ], 422);

                return response()->json($response['body'], $response['statusCode']);
            }
        });
    })
    ->create();