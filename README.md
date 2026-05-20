# EP1: Cliente/Servidor - Instagram (Entrega Parcial 1)

**Linguagem:** PHP 8.2+  
**Framework:** Laravel (REST API)  
**Autenticação:** JWT (JSON Web Token)  
**Banco:** SQLite (padrão) ou MySQL  

## 🚀 Início Rápido

### Pré-requisitos
- PHP 8.2+
- Composer
- SQLite (arquivo `database/database.sqlite`) ou MySQL

> Observação: este repositório está no diretório raiz do projeto (não há pasta `servidor`). Execute os comandos a partir da raiz do projeto.

### Servidor (Laravel 11)

```powershell
# 1. Instalar dependências (na raiz do projeto)
cd "c:\Users\Matheus Henrique\Downloads\ProjetoClienteServidor"
composer install

# 2. Configurar ambiente
copy .env.example .env
php artisan key:generate
php artisan jwt:secret --force

# 3. Criar arquivo SQLite (se for usar SQLite) e rodar migrações
if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File -Path database\database.sqlite | Out-Null }
php artisan migrate --seed

# 4. Iniciar servidor (porta configurável)
php artisan serve --host=0.0.0.0 --port=25000
```

**Servidor ouvindo na rede local (exemplo):** `http://0.0.0.0:25000` (acessível via `http://<IP_DA_MAQUINA>:25000` se firewall permitir)

### Cliente (PHP/HTML/JS)

```powershell
# Abrir cliente estático (na raiz do projeto)
php -S 127.0.0.1:8001 -t cliente
```

**Cliente acessível em:** `http://127.0.0.1:8001`

No cliente, ajuste o campo IP/porta para o servidor (ex: `http://127.0.0.1:25000` ou `http://10.20.8.23:25000`) e use o botão **Testar conexão** antes de operar.

### Logs rápidos

- **Cliente:** painel **Logs do Cliente** na interface (mostra histórico local e testes de conexão). O cliente salva `ep1_api_base_url` e `ep1_jwt_token` no localStorage.
- **Servidor:** executar `php artisan ep1:logs --lines=80` para ver os últimos registros em `storage/logs/laravel.log`.

### Comandos úteis de teste automático

```powershell
# Executa a sequência automática de testes (cadastro, login, consulta, update, delete, logout)
php artisan ep1:test --base-url=http://127.0.0.1:25000

# Ler logs (últimas linhas)
php artisan ep1:logs --lines=80
```

---

## 📡 Estrutura da API

### Autenticação
- Tipo: JWT Bearer Token
- Header: `Authorization: Bearer <token>`
- Gerado em: `POST /usuarios/login`

### Endpoints

| Método | Rota | Autenticação | Descrição |
|--------|------|--------------|-----------|
| `POST` | `/usuarios` | ❌ | Cadastro de usuário |
| `POST` | `/usuarios/login` | ❌ | Login (retorna token) |
| `GET` | `/usuarios/{id}` | ✅ | Obter dados de usuário |
| `PATCH` | `/usuarios/{id}` | ✅ | Atualizar usuário |
| `DELETE` | `/usuarios/{id}` | ✅ | Desativar usuário |
| `POST` | `/usuarios/logout` | ✅ | Logout |

### Formato de Resposta

```json
{
  "status": "sucesso",
  "codigo": "CODIGO_OPERACAO",
  "mensagem": "Descrição da operação em português",
  "dados": { /* dados retornados */ }
}
```

---

## 📝 Fluxo Operacional

### 1. Cadastro de Usuário

```
Cliente → POST /usuarios
  { "nome": "João", "usuario": "joao_silva", "email": "joao@example.com", "senha": "senha123", "biografia": "Dev", "foto": "..." }
Servidor → 201 Created
  { "status": "sucesso", "codigo": "USUARIO_CRIADO", "mensagem": "Usuário cadastrado com sucesso.", "dados": { /* user data */ } }
```

### 2. Login

```
Cliente → POST /usuarios/login
  { "usuario": "joao_silva", "senha": "senha123" }
Servidor → 200 OK
  { "status": "sucesso", "codigo": "LOGIN_SUCESSO", "mensagem": "Login realizado com sucesso.", "dados": { "token": "eyJhbGc..." } }
Cliente → Armazena token em localStorage
```

### 3. Operações Autenticadas

```
Cliente → GET /usuarios/1
  Header: Authorization: Bearer eyJhbGc...
Servidor → 200 OK
  { "status": "sucesso", "codigo": "USUARIO_ENCONTRADO", "mensagem": "Usuário encontrado.", "dados": { /* user data */ } }
```

---

## 🧪 Testes

Executar sequência completa no cliente:
1. **Cadastro** - Criar novo usuário
2. **Login** - Autenticar com as credenciais
3. **Consulta** - Obter dados do usuário autenticado
4. **Atualização** - Modificar campos do usuário
5. **Exclusão** - Desativar usuário
6. **Logout** - Encerrar sessão

Documentação detalhada em: `docs/EP1-Testes.http`

---

## 📚 Documentação

- **[EP1-Protocolo-Mensagens.md](docs/EP1-Protocolo-Mensagens.md)** - Especificação do protocolo
- **[EP1-Checklist-Avaliacao.md](docs/EP1-Checklist-Avaliacao.md)** - Rubrica de avaliação
- **[EP1-Testes.http](docs/EP1-Testes.http)** - Coleção de testes (Postman/VS Code)
- **[Requisitos.md](docs/Requisitos.md)** - Requisitos completos do projeto

---

## ⚙️ Configuração Avançada

### Alterar Porta do Servidor

```bash
php artisan serve --port=9000
```

### Usar MySQL em vez de SQLite

Editar `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=instagram_ep1
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🔒 Segurança

- ✅ Senhas hasheadas com Bcrypt
- ✅ CORS configurado
- ✅ Validação de entrada em todos os endpoints
- ✅ Middleware de autenticação JWT
- ✅ Soft-delete (usuários desativados, não apagados)

---

## 📦 Stack Tecnológico

- **Backend:** Laravel 11, PHP 8.2, SQLite
- **Autenticação:** JWT (tymon/jwt-auth)
- **Frontend:** HTML5, CSS3, JavaScript Vanilla
- **Versão:** Git (commits: 0600602, 0b5a5bc)

---

## ✉️ Suporte

Para dúvidas sobre o protocolo, consulte `docs/EP1-Protocolo-Mensagens.md`.  
Para dúvidas sobre a rubrica, consulte `docs/EP1-Checklist-Avaliacao.md`.

---

## 📦 Entrega (zip) e instruções para o avaliador

- O zip disponibilizado na Desktop é uma versão enxuta: **exclui** `vendor/`, `storage/`, `.git`, `node_modules` e o arquivo `database/database.sqlite` gerado. Ele inclui `composer.json`, `composer.lock` e `.env.example`.
- Antes de rodar no computador do avaliador, execute (na raiz do projeto):

```powershell
# 1. Instalar dependências
composer install

# 2. Criar .env a partir do exemplo e gerar chaves
copy .env.example .env
php artisan key:generate
php artisan jwt:secret --force

# 3. (SQLite) criar arquivo e rodar migrações
if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File -Path database\database.sqlite | Out-Null }
php artisan migrate --seed

# 4. Iniciar servidor e cliente
php artisan serve --host=0.0.0.0 --port=25000
php -S 127.0.0.1:8001 -t cliente
```

- Se preferir incluir o arquivo `database/database.sqlite` no zip (para manter dados de teste), avise que eu gero uma versão alternativa do zip contendo o arquivo de banco.

---