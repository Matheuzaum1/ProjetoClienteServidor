<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['usuario' => 'admin'],
            [
                'nome' => 'Administrador',
                'email' => 'admin@localhost',
                'password' => Hash::make('admin123'),
                'biografia' => 'Usuário administrador do sistema',
                'ativo' => true,
                'tipo_usuario' => 'adm',
            ]
        );

        User::updateOrCreate(
            ['usuario' => 'user1'],
            [
                'nome' => 'Usuário Um',
                'email' => 'user1@localhost',
                'password' => Hash::make('senha123'),
                'biografia' => 'Usuário comum para testes',
                'ativo' => true,
                'tipo_usuario' => 'comum',
            ]
        );

        User::updateOrCreate(
            ['usuario' => 'user2'],
            [
                'nome' => 'Usuário Dois',
                'email' => 'user2@localhost',
                'password' => Hash::make('senha123'),
                'biografia' => 'Outro usuário comum',
                'ativo' => true,
                'tipo_usuario' => 'comum',
            ]
        );
    }
}
