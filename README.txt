PROJETO CLIENTE/SERVIDOR - EP1 (Instagram)

Autor: Matheus
Repositorio: ProjetoClienteServidor

==================================================
1) RESUMO DO QUE ESTA PRONTO
==================================================

Servidor (Laravel + JWT):
- Cadastro de usuario comum
- Login
- Consulta de usuario por ID
- Atualizacao de usuario
- Exclusao/desativacao de usuario
- Logout

Cliente (Web PHP/HTML/JS):
- Campo para URL/IP e porta do servidor
- Formularios para todas as operacoes da EP1
- Reuso automatico de token JWT apos login

==================================================
2) REQUISITOS DE AMBIENTE
==================================================

- PHP 8.2+ (com extensoes comuns de Laravel)
- Composer
- MySQL 8+ (ou compativel)

==================================================
3) CONFIGURACAO DE BANCO
==================================================

3.1 Criar banco:
- Nome sugerido: instagram_ep1

3.2 Configurar arquivo .env na raiz:
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=instagram_ep1
- DB_USERNAME=<seu_usuario>
- DB_PASSWORD=<sua_senha>

Observacao:
- APP_KEY e JWT_SECRET ja podem estar preenchidos.
- Se nao estiverem, use os comandos da secao 4.

==================================================
4) SUBIR O SERVIDOR (API)
==================================================

Na raiz do projeto:

1. Instalar dependencias:
   composer install

2. Gerar chave da aplicacao (se necessario):
   php artisan key:generate

3. Gerar segredo JWT (se necessario):
   php artisan jwt:secret --force

4. Executar migrations:
   php artisan migrate

5. Iniciar API com porta configuravel:
   php artisan serve --host=0.0.0.0 --port=8080

A API ficara disponivel em:
- http://127.0.0.1:8080

==================================================
5) SUBIR O CLIENTE
==================================================

Em outro terminal, na raiz do projeto:

- php -S 127.0.0.1:5500 -t cliente

Abrir no navegador:
- http://127.0.0.1:5500

No cliente:
- Informar servidor como http://127.0.0.1:8080
- Clicar em Salvar

==================================================
6) ENDPOINTS EP1
==================================================

- POST   /api/usuarios
- POST   /api/usuarios/login
- GET    /api/usuarios/{id}
- PATCH  /api/usuarios/{id}
- DELETE /api/usuarios/{id}
- POST   /api/usuarios/logout

Padrao de auth para rotas protegidas:
- Authorization: Bearer <token>

==================================================
7) ROTEIRO DE DEMONSTRACAO EM SALA
==================================================

1. Definir URL/IP e porta no cliente.
2. Fazer cadastro de usuario.
3. Fazer login e obter token.
4. Consultar usuario por ID.
5. Atualizar dados do usuario.
6. Consultar novamente para provar atualizacao.
7. Excluir/desativar usuario.
8. Fazer logout.

==================================================
8) ARQUIVOS IMPORTANTES
==================================================

Documentacao:
- docs/EP1-Roteiro-Laravel.md
- docs/EP1-Protocolo-Mensagens.md
- docs/EP1-Checklist-Avaliacao.md
- docs/EP1-Testes.http

Servidor:
- routes/api.php
- app/Http/Controllers/AuthController.php
- app/Http/Controllers/UsuarioController.php
- app/Models/User.php
- database/migrations/2026_04_27_000000_create_users_table.php

Cliente:
- cliente/index.php
- cliente/assets/app.js
- cliente/assets/styles.css

==================================================
9) OBSERVACOES PARA AVALIACAO
==================================================

- Nao modificar codigo no momento da apresentacao.
- Levar tudo testado antecipadamente.
- Garantir que cliente e servidor iniciam apenas com os comandos acima.
- Se for usar outra maquina, ajustar somente variaveis de ambiente e URL/IP.
