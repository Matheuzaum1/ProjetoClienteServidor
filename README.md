# Projeto Cliente/Servidor - Instagram (EP-3)

API REST em Laravel com autenticação JWT e cliente web para testes completos da EP-3 (posts, curtidas, usuários logados).

## Pré-requisitos
- PHP 8.2+
- Composer
- SQLite (padrão) ou MySQL

## Setup para o professor (Windows)

Execute na raiz do projeto:

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan jwt:secret --force

if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File -Path database\database.sqlite | Out-Null }
php artisan migrate:fresh --force
php artisan db:seed --class="Database\Seeders\AdminUserSeeder" --force
```

## Iniciar servidor e cliente

Opção 1 (scripts prontos):

```powershell
start.bat
start-client.bat
```

Opção 2 (manual):

```powershell
php artisan serve --host=0.0.0.0 --port=25000
php -S 127.0.0.1:8001 -t cliente
```

## URLs de acesso
- Cliente principal: `http://127.0.0.1:8001`
- Painel de testes: `http://127.0.0.1:8001/testes`
- API: `http://127.0.0.1:25000`

## Usuários de teste (seed)
- Admin: `admin` / `admin123`
- Comum: `user1` / `senha123`
- Comum: `user2` / `senha123`

## Como testar
1. Abra `http://127.0.0.1:8001/testes`
2. Faça login com algum usuário.
3. Use os cards para criar, listar, consultar, atualizar e deletar posts.
4. Use os cards de Curtir/Descurtir para interagir com posts.
5. Na aba "Explorar", veja a lista de usuários e usuários logados.

## Endpoints principais

| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/usuarios` | Cadastro |
| `POST` | `/usuarios/login` | Login |
| `POST` | `/usuarios/logout` | Logout |
| `GET` | `/usuarios` | Listar usuários |
| `GET` | `/usuarios/logados` | Usuários logados |
| `GET` | `/usuarios/{id}` | Mostrar usuário |
| `PATCH` | `/usuarios/{id}` | Atualizar usuário |
| `DELETE` | `/usuarios/{id}` | Excluir usuário |
| `GET` | `/usuarios/{id}/posts` | Listar posts |
| `POST` | `/usuarios/{id}/posts` | Criar post |
| `GET` | `/usuarios/{id}/posts/{id}` | Mostrar post |
| `PATCH` | `/usuarios/{id}/posts/{id}` | Atualizar post |
| `DELETE` | `/usuarios/{id}/posts/{id}` | Excluir post |
| `POST` | `/usuarios/{id}/posts/{id}` | Curtir post |
| `DELETE` | `/usuarios/{id}/posts/{id}/curtir` | Descurtir post |

## Coleta de logs (cliente + servidor)

Script auxiliar:

```powershell
.\collect-ep2-logs.ps1
```

Ele copia `storage/logs/laravel.log` para `logs` e gera instruções de exportação do log do cliente (localStorage) no navegador.

## Observações
- O protocolo OpenAPI está em `docs/allvesleooorganizati-instagram-api-gerenciamento-de-usuarios-1.0.1-resolved.json`.
- O projeto usa padrão de resposta JSON: `{ status, codigo, mensagem, dados }`.