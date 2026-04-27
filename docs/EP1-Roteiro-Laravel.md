# EP1 - Roteiro Laravel (Cliente/Servidor)

Este roteiro implementa apenas o escopo da EP-1:
- Cadastro de usuario comum
- Login
- Consulta de usuario
- Atualizacao de usuario
- Exclusao/desativacao de usuario
- Logout

## 1. Criar projeto Laravel

```bash
composer create-project laravel/laravel instagram-api
cd instagram-api
```

## 2. Instalar JWT

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\\JWTAuth\\Providers\\LaravelServiceProvider"
php artisan jwt:secret
```

## 3. Configurar banco

Atualize o `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=instagram_ep1
DB_USERNAME=root
DB_PASSWORD=
```

## 4. Criar migration de usuarios

Se sua tabela `users` ainda nao atende ao requisito, ajuste para conter:
- nome
- usuario (unico)
- email (unico)
- senha (hash)
- biografia (opcional)
- foto_url (opcional)
- ativo (boolean, default true)

Exemplo rapido:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nome', 60);
    $table->string('usuario', 30)->unique();
    $table->string('email', 35)->unique();
    $table->string('password');
    $table->string('biografia', 150)->nullable();
    $table->string('foto_url')->nullable();
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

## 5. Model User

No model `app/Models/User.php`:

```php
<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Foundation\\Auth\\User as Authenticatable;
use Illuminate\\Notifications\\Notifiable;
use Illuminate\\Database\\Eloquent\\SoftDeletes;
use Tymon\\JWTAuth\\Contracts\\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nome',
        'usuario',
        'email',
        'password',
        'biografia',
        'foto_url',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
```

## 6. Requests de validacao

Crie:
- `app/Http/Requests/CadastroRequest.php`
- `app/Http/Requests/LoginRequest.php`
- `app/Http/Requests/AtualizacaoUsuarioRequest.php`

### CadastroRequest

```php
<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class CadastroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z\\s]+$/'],
            'usuario' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', 'unique:users,usuario'],
            'email' => ['required', 'email', 'min:10', 'max:35', 'unique:users,email'],
            'senha' => ['required', 'string', 'min:8', 'max:24', 'regex:/^[A-Za-z0-9]+$/'],
            'biografia' => ['nullable', 'string', 'max:150'],
            'foto' => ['nullable', 'string'],
        ];
    }
}
```

### LoginRequest

```php
<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => ['required', 'string'],
            'senha' => ['required', 'string'],
        ];
    }
}
```

### AtualizacaoUsuarioRequest

```php
<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class AtualizacaoUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z\\s]+$/'],
            'usuario' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', 'unique:users,usuario,' . $this->route('id')],
            'email' => ['required', 'email', 'min:10', 'max:35', 'unique:users,email,' . $this->route('id')],
            'biografia' => ['nullable', 'string', 'max:150'],
            'foto' => ['nullable', 'string'],
            'senha' => ['nullable', 'string', 'min:8', 'max:24', 'regex:/^[A-Za-z0-9]+$/'],
        ];
    }
}
```

## 7. Controllers

Crie:
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/UsuarioController.php`

### AuthController

```php
<?php

namespace App\\Http\\Controllers;

use App\\Http\\Requests\\LoginRequest;
use App\\Models\\User;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Support\\Facades\\Hash;
use Tymon\\JWTAuth\\Facades\\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('usuario', $request->usuario)->where('ativo', true)->first();

        if (!$user || !Hash::check($request->senha, $user->password)) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'CREDENCIAIS_INVALIDAS',
                'mensagem' => 'Usuario ou senha invalidos',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'LOGIN_SUCESSO',
            'mensagem' => 'Login realizado com sucesso',
            'dados' => [
                'token' => $token,
                'usuario' => [
                    'id' => (string) $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'usuario' => $user->usuario,
                ],
            ],
        ], 200);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'LOGOUT_SUCESSO',
            'mensagem' => 'Logout realizado com sucesso',
            'dados' => (object) [],
        ], 200);
    }
}
```

### UsuarioController

```php
<?php

namespace App\\Http\\Controllers;

use App\\Http\\Requests\\AtualizacaoUsuarioRequest;
use App\\Http\\Requests\\CadastroRequest;
use App\\Models\\User;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Support\\Facades\\Hash;

class UsuarioController extends Controller
{
    public function store(CadastroRequest $request): JsonResponse
    {
        $user = User::create([
            'nome' => $request->nome,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'password' => Hash::make($request->senha),
            'biografia' => $request->biografia,
            'foto_url' => $request->foto,
            'ativo' => true,
        ]);

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_CRIADO',
            'mensagem' => 'Usuario cadastrado com sucesso',
            'dados' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'usuario' => $user->usuario,
                'biografia' => $user->biografia,
                'foto_url' => $user->foto_url,
            ],
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::where('ativo', true)->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'USUARIO_NAO_ENCONTRADO',
                'mensagem' => 'Usuario nao encontrado',
            ], 404);
        }

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_ENCONTRADO',
            'mensagem' => 'Dados do usuario recuperados',
            'dados' => [
                'id' => (string) $user->id,
                'nome_completo' => $user->nome,
                'usuario' => $user->usuario,
                'email' => $user->email,
                'biografia' => $user->biografia,
                'foto_url' => $user->foto_url,
            ],
        ], 200);
    }

    public function update(AtualizacaoUsuarioRequest $request, string $id): JsonResponse
    {
        $user = User::where('ativo', true)->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'USUARIO_NAO_ENCONTRADO',
                'mensagem' => 'Usuario nao encontrado',
            ], 404);
        }

        $payload = [
            'nome' => $request->nome,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'biografia' => $request->biografia,
            'foto_url' => $request->foto,
        ];

        if ($request->filled('senha')) {
            $payload['password'] = Hash::make($request->senha);
        }

        $user->update($payload);

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_ATUALIZADO',
            'mensagem' => 'Usuario atualizado com sucesso',
            'dados' => [
                'id' => (string) $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'usuario' => $user->usuario,
            ],
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'erro',
                'codigo' => 'USUARIO_NAO_ENCONTRADO',
                'mensagem' => 'Usuario nao encontrado',
            ], 404);
        }

        $user->ativo = false;
        $user->save();
        $user->delete();

        return response()->json([
            'status' => 'sucesso',
            'codigo' => 'USUARIO_REMOVIDO',
            'mensagem' => 'Usuario removido com sucesso',
            'dados' => (object) [],
        ], 200);
    }
}
```

## 8. Rotas API

No arquivo `routes/api.php`:

```php
<?php

use App\\Http\\Controllers\\AuthController;
use App\\Http\\Controllers\\UsuarioController;
use Illuminate\\Support\\Facades\\Route;

Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::post('/usuarios/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/usuarios/logout', [AuthController::class, 'logout']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
});
```

## 9. Auth guard JWT

No arquivo `config/auth.php`, garantir:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

## 10. Rodar migration e servidor

```bash
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8080
```

Importante para a avaliacao:
- O servidor deve aceitar porta configuravel (exemplo acima)
- O cliente deve permitir informar IP e porta do servidor

## 11. O que mostrar no dia

1. Cadastro: `POST /usuarios`
2. Login: `POST /usuarios/login`
3. Consulta usuario: `GET /usuarios/{id}` com Bearer token
4. Atualizacao: `PATCH /usuarios/{id}` com Bearer token
5. Exclusao: `DELETE /usuarios/{id}` com Bearer token
6. Logout: `POST /usuarios/logout` com Bearer token

Sem alterar codigo no momento da avaliacao.
