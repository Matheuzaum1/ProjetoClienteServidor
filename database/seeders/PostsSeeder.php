<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('Nenhum usuário encontrado. Execute AdminUserSeeder primeiro.');
            return;
        }

        $posts = [
            ['legenda' => 'Olá, mundo! Este é meu primeiro post.', 'imagem' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='],
            ['legenda' => 'Bom dia a todos! Como estão hoje?', 'imagem' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='],
            ['legenda' => 'Testando o upload de imagens no servidor', 'imagem' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='],
            ['legenda' => 'Aula de hoje foi muito produtiva!', 'imagem' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='],
            ['legenda' => 'Finalmente terminando o projeto de Cliente/Servidor', 'imagem' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='],
        ];

        $autorPosts = [
            ['legenda' => 'Postagem criada pelo autor1 — testando o CRUD', 'imagem' => ''],
            ['legenda' => 'Autor1 editou esta legenda para mostrar atualização de post', 'imagem' => 'https://picsum.photos/400/300?random=1'],
        ];

        foreach ($users as $user) {
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $postData = $posts[array_rand($posts)];
                Post::create([
                    'user_id' => $user->id,
                    'imagem' => $postData['imagem'],
                    'legenda' => $postData['legenda'] . ' (Post #' . ($i + 1) . ' de @' . $user->usuario . ')',
                ]);
            }
        }

        $autor = User::where('usuario', 'autor1')->first();
        if ($autor) {
            foreach ($autorPosts as $ap) {
                Post::create([
                    'user_id' => $autor->id,
                    'imagem' => $ap['imagem'],
                    'legenda' => $ap['legenda'],
                ]);
            }
        }

        $this->command->info('Posts de exemplo criados com sucesso!');
    }
}
