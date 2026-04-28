<?php

namespace App\Support;

final class ApiResponse
{
    public static function success(string $codigo, string $mensagem, array $dados = [], int $statusCode = 200): array
    {
        return [
            'body' => [
                'status' => 'sucesso',
                'codigo' => $codigo,
                'mensagem' => $mensagem,
                'dados' => $dados,
            ],
            'statusCode' => $statusCode,
        ];
    }

    public static function error(string $codigo, string $mensagem, array $dados = [], int $statusCode = 400): array
    {
        return [
            'body' => [
                'status' => 'erro',
                'codigo' => $codigo,
                'mensagem' => $mensagem,
                'dados' => $dados,
            ],
            'statusCode' => $statusCode,
        ];
    }
}