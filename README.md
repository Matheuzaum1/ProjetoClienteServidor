# EP1: Cliente/Servidor - Instagram (Entrega Parcial 1)

**Linguagem:** PHP 8.2+  
**Framework:** Laravel (REST API)  
**Autenticação:** JWT (JSON Web Token)  
**Banco:** SQLite (padrão) ou MySQL  

---

## 📋 Requisitos Atendidos (EP-1)

### Cliente (1,0 ponto)
- ✅ **a)** Enviar cadastro de usuário comum para o servidor (0,2)
- ✅ **b)** Enviar login para o servidor (0,2)
- ✅ **c)** Pedir dados de cadastro do usuário para o servidor (0,2)
- ✅ **d)** Enviar atualização dos dados do usuário (0,2)
- ✅ **e)** Enviar pedido para apagar cadastro (0,1)
- ✅ **f)** Enviar logout para o servidor (0,1)

### Servidor (1,0 ponto)
- ✅ **g)** Processar cadastro de usuário comum (0,2)
- ✅ **h)** Processar login com validação (0,2)
- ✅ **i)** Enviar dados de cadastro ao cliente (0,2)
- ✅ **j)** Processar atualização de usuário (0,2)
- ✅ **k)** Apagar/desativar cadastro (0,1)
- ✅ **l)** Processar logout (0,1)

### Critérios Obrigatórios
- ✅ Protocolo de troca de mensagens entregue
- ✅ Código-fonte versionado no Git
- ✅ Servidor com porta configurável
- ✅ Cliente com campo de IP/porta do servidor
- ✅ Comunicação bidirecional cliente ↔ servidor
- ✅ Respostas JSON com estrutura padrão

---

## 🚀 Início Rápido

### Pré-requisitos
- PHP 8.2+
- Composer
- SQLite ou MySQL

### Servidor (Laravel 11)

```bash
# 1. Instalar dependências
cd servidor
composer install

# 2. Configurar ambiente
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# 3. Preparar banco de dados
php artisan migrate --seed

# 4. Iniciar servidor (porta configurável)
php artisan serve --port=8000
```

**Servidor rodando em:** `http://localhost:8000`

### Cliente (PHP/HTML/JS)

```bash
# Abrir no navegador
php -S localhost:8001 -t cliente/
```

**Cliente acessível em:** `http://localhost:8001`

Inserir IP/porta do servidor na interface (ex: `http://localhost:8000`)

---

## 📡 Estrutura da API

### Autenticação
- Tipo: JWT Bearer Token
- Header: `Authorization: Bearer <token>`
- Gerado em: `POST /api/usuarios/login`

### Endpoints

| Método | Rota | Autenticação | Descrição |
|--------|------|--------------|-----------|
| `POST` | `/api/usuarios` | ❌ | Cadastro de usuário |
| `POST` | `/api/usuarios/login` | ❌ | Login (retorna token) |
| `GET` | `/api/usuarios/{id}` | ✅ | Obter dados de usuário |
| `PATCH` | `/api/usuarios/{id}` | ✅ | Atualizar usuário |
| `DELETE` | `/api/usuarios/{id}` | ✅ | Desativar usuário |
| `POST` | `/api/usuarios/logout` | ✅ | Logout |

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
Cliente → POST /api/usuarios
  { "nome": "João", "usuario": "joao_silva", "email": "joao@example.com", "senha": "senha123", "biografia": "Dev", "foto": "..." }
Servidor → 201 Created
  { "status": "sucesso", "codigo": "USUARIO_CRIADO", "mensagem": "Usuário cadastrado com sucesso.", "dados": { /* user data */ } }
```

### 2. Login

```
Cliente → POST /api/usuarios/login
  { "usuario": "joao_silva", "senha": "senha123" }
Servidor → 200 OK
  { "status": "sucesso", "codigo": "LOGIN_SUCESSO", "mensagem": "Login realizado com sucesso.", "dados": { "token": "eyJhbGc..." } }
Cliente → Armazena token em localStorage
```

### 3. Operações Autenticadas

```
Cliente → GET /api/usuarios/1
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