<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment('Projeto Cliente/Servidor EP1');
})->purpose('Display an inspiring message');

Artisan::command('ep1:test {--base-url=http://127.0.0.1:8080}', function () {
    $baseUrl = rtrim((string) $this->option('base-url'), '/');
    $stamp = now()->format('YmdHis');

    $payloadCadastro = [
        'nome' => 'Usuario Teste',
        'usuario' => 'usuario_teste_' . $stamp,
        'email' => 'usuario_teste_' . $stamp . '@email.com',
        'senha' => 'Senha123',
        'biografia' => 'Bio inicial do teste',
        'foto' => 'https://example.com/foto.jpg',
    ];

    $this->info('1/6 Cadastro...');
    $cadastro = Http::acceptJson()->post($baseUrl . '/api/usuarios', $payloadCadastro);

    if (!$cadastro->successful()) {
        $this->error('Falha no cadastro.');
        $this->line($cadastro->body());
        return self::FAILURE;
    }

    $usuarioId = data_get($cadastro->json(), 'dados.id');
    if (!$usuarioId) {
        $this->error('Cadastro sem ID retornado.');
        $this->line($cadastro->body());
        return self::FAILURE;
    }

    $this->info('2/6 Login...');
    $login = Http::acceptJson()->post($baseUrl . '/api/usuarios/login', [
        'usuario' => $payloadCadastro['usuario'],
        'senha' => $payloadCadastro['senha'],
    ]);

    if (!$login->successful()) {
        $this->error('Falha no login.');
        $this->line($login->body());
        return self::FAILURE;
    }

    $token = data_get($login->json(), 'dados.token');
    if (!$token) {
        $this->error('Login sem token retornado.');
        $this->line($login->body());
        return self::FAILURE;
    }

    $authed = Http::acceptJson()->withToken($token);

    $this->info('3/6 Consulta...');
    $consulta = $authed->get($baseUrl . '/api/usuarios/' . $usuarioId);
    if (!$consulta->successful()) {
        $this->error('Falha na consulta.');
        $this->line($consulta->body());
        return self::FAILURE;
    }

    $this->info('4/6 Atualizacao...');
    $atualizacao = $authed->patch($baseUrl . '/api/usuarios/' . $usuarioId, [
        'nome' => 'Usuario Teste Atualizado',
        'usuario' => $payloadCadastro['usuario'],
        'email' => $payloadCadastro['email'],
        'biografia' => 'Bio atualizada automaticamente',
        'foto' => 'https://example.com/foto-atualizada.jpg',
    ]);

    if (!$atualizacao->successful()) {
        $this->error('Falha na atualizacao.');
        $this->line($atualizacao->body());
        return self::FAILURE;
    }

    $this->info('5/6 Exclusao...');
    $exclusao = $authed->delete($baseUrl . '/api/usuarios/' . $usuarioId);
    if (!$exclusao->successful()) {
        $this->error('Falha na exclusao.');
        $this->line($exclusao->body());
        return self::FAILURE;
    }

    $this->info('6/6 Logout...');
    $logout = $authed->post($baseUrl . '/api/usuarios/logout', []);
    if (!$logout->successful()) {
        $this->error('Falha no logout.');
        $this->line($logout->body());
        return self::FAILURE;
    }

    $this->info('Auto-teste concluido com sucesso.');
    return self::SUCCESS;
})->purpose('Executa o fluxo completo do EP1 contra a API');