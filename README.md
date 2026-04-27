# Projeto Cliente/Servidor - Instagram EP1

Base Laravel para a primeira entrega parcial.

## Escopo

- Cadastro de usuario comum
- Login com JWT
- Consulta de usuario por ID
- Atualizacao de usuario
- Exclusao/desativacao de usuario
- Logout

## Rotas

- `POST /api/usuarios`
- `POST /api/usuarios/login`
- `GET /api/usuarios/{id}`
- `PATCH /api/usuarios/{id}`
- `DELETE /api/usuarios/{id}`
- `POST /api/usuarios/logout`

## Observacoes

- O cliente deve permitir informar IP e porta do servidor.
- O servidor pode ser iniciado com porta configuravel.
- O token deve ser enviado no cabeçalho `Authorization: Bearer <token>`.

## Proximos passos

1. Instalar dependencias com Composer.
2. Criar o banco e rodar as migrations.
3. Configurar JWT secret.
4. Testar com o arquivo `docs/EP1-Testes.http`.