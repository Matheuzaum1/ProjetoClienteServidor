# EP1 - Checklist de Validacao e Avaliacao

## 1) Status tecnico atual

- [x] Dependencias instaladas com `composer install`
- [x] Servidor Laravel inicia com `php artisan serve`
- [x] Rotas da API conferidas com `php artisan route:list`
- [x] JWT configurado com `php artisan jwt:secret --force`
- [x] Cliente web criado em `cliente/` com campo de IP/porta
- [x] Documentacao de apoio criada na pasta `docs/`

## 2) EP-1 - Cliente (1,0)

- [ ] a) Envia cadastro de usuario comum (0,2)
- [ ] b) Envia login (0,2)
- [ ] c) Pede dados de cadastro do usuario comum (0,2)
- [ ] d) Envia atualizacao de dados do usuario comum (0,2)
- [ ] e) Envia pedido para apagar cadastro de usuario comum (0,1)
- [ ] f) Envia logout (0,1)

## 3) EP-1 - Servidor (1,0)

- [ ] g) Processa cadastro corretamente (0,2)
- [ ] h) Processa login corretamente (0,2)
- [ ] i) Envia dados de cadastro do usuario comum ao cliente (0,2)
- [ ] j) Processa atualizacao corretamente (0,2)
- [ ] k) Apaga/desativa cadastro de usuario comum (0,1)
- [ ] l) Processa logout corretamente (0,1)

## 4) Regras operacionais obrigatorias

- [x] Protocolo de troca de mensagens entregue antes da avaliacao
- [x] Codigo fonte enviado antes da avaliacao
- [x] Servidor com porta configuravel
- [x] Cliente com campo de IP/URL do servidor
- [x] Cliente com campo de porta configuravel via URL base
- [x] Cliente apto a trocar mensagens com o servidor
- [x] Servidor apto a trocar mensagens com o cliente
- [ ] Nenhuma alteracao no codigo no momento da avaliacao

## 5) Evidencias que precisam ser conferidas antes da entrega

- [ ] Banco de dados criado e migrations executadas
- [ ] Conta de teste criada com sucesso
- [ ] Login retorna token JWT valido
- [ ] GET /usuarios/{id} retorna dados corretos com Bearer token
- [ ] PATCH /usuarios/{id} atualiza o registro esperado
- [ ] DELETE /usuarios/{id} desativa ou remove o usuario
- [ ] POST /usuarios/logout invalida a sessao/token atual
- [ ] Cliente web executa todas as operacoes sem erro de CORS
- [ ] Capturas de tela ou respostas salvas para a apresentacao

## 6) Sequencia curta para demonstrar em sala

1. Definir URL/IP e porta no cliente.
2. Cadastrar usuario.
3. Fazer login e obter token.
4. Consultar usuario por ID.
5. Atualizar dados do usuario.
6. Consultar novamente para provar a atualizacao.
7. Excluir/desativar usuario.
8. Fazer logout.

## 7) Observacao final

- Nao alterar o codigo durante a avaliacao.
- Levar o servidor e o cliente testados com antecedencia.
- Se a apresentacao for em outra maquina, ajustar apenas a URL/IP e a porta do servidor.
