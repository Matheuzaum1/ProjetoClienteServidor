# Estratégia de Tokens (Access + Refresh)

Resumo rápido para compatibilidade entre projetos (alunos A/B): um fluxo padrão, seguro e interoperável.

1. Objetivo
- Fornecer autenticação com JWT (access token) e refresh token revogável para permitir logout efetivo e renovação segura de tokens.

2. Endpoints esperados (contrato)
- `POST /usuarios/login` — corpo: `{ usuario, senha }`. Retorna `{ dados: { token, refresh_token, usuario } }`.
- `POST /token/refresh` — corpo: `{ refresh_token }`. Retorna `{ dados: { token, refresh_token } }` (rotaciona refresh).
- `POST /usuarios/logout` — opcional: corpo `{ refresh_token }`. Revoga o refresh token e invalida o access token se fornecido.

3. Regras de implementação no servidor
- Access token: JWT curto (ex.: 15min - configurável). Assinado e validado normalmente.
- Refresh token: string aleatória longa (ex.: 64 chars) armazenada no servidor com hash, `expires_at` e flag `revoked`.
- Ao usar `refresh`, o servidor revoga o refresh token anterior e emite um novo (rotacionamento).
- Logout: revoga refresh token recebido e invalida access token se presente (blacklist ou equivalente).

4. Regras de implementação no cliente
- Armazenar access token e refresh token (ideal: access em memória e refresh em HttpOnly cookie; para simplicidade neste projeto usamos `localStorage`).
- Antes de cada requisição que exige autenticação, anexar `Authorization: Bearer <access token>`.
- Se uma requisição falhar com 401, chamar `POST /token/refresh` com o `refresh_token`. Se renovado com sucesso, refazer a requisição original.
- Ao logout, enviar `refresh_token` para `/usuarios/logout` para revogação e limpar tokens localmente.

5. Compatibilidade entre projetos
- Contrato claro (endpoints e campos) torna a integração entre projetos trivial: professor escolhe aluno A como cliente e aluno B como servidor; cliente deve usar os endpoints acima e o fluxo funcionará.

6. Considerações de segurança
- Preferir armazenar `refresh_token` em cookie HttpOnly e `SameSite` quando for um ambiente real.
- Usar HTTPS em produção.
- Implementar expiração curta para access tokens e revogação/rotacionamento para refresh tokens.

7. Arquivos relacionados no repositório
- `app/Http/Controllers/AuthController.php` — implementação de login/logout/refresh
- `database/migrations/*_create_refresh_tokens_table.php` — tabela de refresh tokens
- `app/Models/RefreshToken.php` — model
- `cliente/assets/app-login.js` e `cliente/testes/assets/app.js` — cliente usando refresh + retry automático

Se quiser, eu adapto o cliente para usar cookie HttpOnly para refresh (requer pequenas mudanças no servidor). Também posso gerar um diagrama rápido do fluxo.
