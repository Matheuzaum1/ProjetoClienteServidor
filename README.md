# Projeto Cliente/Servidor - Instagram (EP-2)

API REST em Laravel com autenticação JWT e cliente web para testes das regras de autorização da EP-2.

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

## Como testar "Listar usuários" (mais simples)
1. Abra `http://127.0.0.1:8001/testes`
2. Faça login com usuário admin.
3. Clique no botão **"Listar usuários agora"** no topo da tela.
4. O resultado aparece no bloco **"Usuários retornados pelo servidor"**.

## Endpoints principais
- `POST /usuarios`
- `POST /usuarios/login`
- `POST /usuarios/logout`
- `GET /usuarios`
- `GET /usuarios/{id}`
- `PATCH /usuarios/{id}`
- `DELETE /usuarios/{id}`

## Coleta de logs (cliente + servidor)

Script auxiliar:

```powershell
.\collect-ep2-logs.ps1
```

Ele copia `storage/logs/laravel.log` para `logs` e gera instruções de exportação do log do cliente (localStorage) no navegador.

## Observações
- O protocolo OpenAPI está em `docs/allvesleooorganizati-instagram-api-gerenciamento-de-usuarios-1.0.1-resolved.json`.
- O projeto usa padrão de resposta JSON: `{ status, codigo, mensagem, dados }`.