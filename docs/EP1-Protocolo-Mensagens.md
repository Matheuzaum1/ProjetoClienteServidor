# EP1 - Protocolo de Mensagens (HTTP/JSON)

Padrao de resposta para sucesso e erro:

```json
{
  "status": "sucesso|erro",
  "codigo": "CODIGO_SEMANTICO",
  "mensagem": "descricao",
  "dados": {}
}
```

## 1) Cadastro de usuario

- Metodo: `POST`
- URL: `/usuarios`
- Auth: nao

Request:

```json
{
  "nome": "Joao da Silva",
  "usuario": "joao_silva",
  "email": "joao@email.com",
  "senha": "Senha123",
  "biografia": "Minha bio",
  "foto": "https://site.com/foto.jpg"
}
```

Response 201:

```json
{
  "status": "sucesso",
  "codigo": "USUARIO_CRIADO",
  "mensagem": "Usuario cadastrado com sucesso",
  "dados": {
    "id": "1",
    "nome": "Joao da Silva",
    "usuario": "joao_silva",
    "email": "joao@email.com"
  }
}
```

## 2) Login

- Metodo: `POST`
- URL: `/usuarios/login`
- Auth: nao

Request:

```json
{
  "usuario": "joao_silva",
  "senha": "Senha123"
}
```

Response 200:

```json
{
  "status": "sucesso",
  "codigo": "LOGIN_SUCESSO",
  "mensagem": "Login realizado com sucesso",
  "dados": {
    "token": "jwt_token",
    "usuario": {
      "id": "1",
      "nome": "Joao da Silva",
      "email": "joao@email.com"
    }
  }
}
```

## 3) Obter usuario por id

- Metodo: `GET`
- URL: `/usuarios/{id}`
- Auth: sim (Bearer token)

Header:

```http
Authorization: Bearer jwt_token
```

Response 200:

```json
{
  "status": "sucesso",
  "codigo": "USUARIO_ENCONTRADO",
  "mensagem": "Dados do usuario recuperados",
  "dados": {
    "id": "1",
    "nome_completo": "Joao da Silva",
    "usuario": "joao_silva",
    "email": "joao@email.com",
    "biografia": "Minha bio",
    "foto_url": "https://site.com/foto.jpg"
  }
}
```

## 4) Atualizar usuario por id

- Metodo: `PATCH`
- URL: `/usuarios/{id}`
- Auth: sim (Bearer token)

Request:

```json
{
  "nome": "Joao S. Silva",
  "usuario": "joao_silva",
  "email": "joao@email.com",
  "biografia": "Bio atualizada"
}
```

Response 200:

```json
{
  "status": "sucesso",
  "codigo": "USUARIO_ATUALIZADO",
  "mensagem": "Usuario atualizado com sucesso",
  "dados": {
    "id": "1",
    "nome": "Joao S. Silva",
    "usuario": "joao_silva",
    "email": "joao@email.com"
  }
}
```

## 5) Excluir/desativar usuario por id

- Metodo: `DELETE`
- URL: `/usuarios/{id}`
- Auth: sim (Bearer token)

Response 200:

```json
{
  "status": "sucesso",
  "codigo": "USUARIO_REMOVIDO",
  "mensagem": "Usuario removido com sucesso",
  "dados": {}
}
```

## 6) Logout

- Metodo: `POST`
- URL: `/usuarios/logout`
- Auth: sim (Bearer token)

Response 200:

```json
{
  "status": "sucesso",
  "codigo": "LOGOUT_SUCESSO",
  "mensagem": "Logout realizado com sucesso",
  "dados": {}
}
```

## Erros padrao importantes

- 400: dados incompletos ou invalidos
- 401: nao autenticado, token invalido ou expirado
- 404: usuario nao encontrado
- 409: usuario/email ja cadastrado
