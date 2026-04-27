# EP1 - Checklist de Avaliacao (2,0 pontos)

## Cliente (1,0)

- [ ] a) Envia cadastro de usuario comum (0,2)
- [ ] b) Envia login (0,2)
- [ ] c) Pede dados de cadastro do usuario comum (0,2)
- [ ] d) Envia atualizacao de dados do usuario comum (0,2)
- [ ] e) Envia pedido para apagar cadastro de usuario comum (0,1)
- [ ] f) Envia logout (0,1)

## Servidor (1,0)

- [ ] g) Processa cadastro corretamente (0,2)
- [ ] h) Processa login corretamente (0,2)
- [ ] i) Envia dados de cadastro do usuario comum ao cliente (0,2)
- [ ] j) Processa atualizacao corretamente (0,2)
- [ ] k) Apaga/desativa cadastro de usuario comum (0,1)
- [ ] l) Processa logout corretamente (0,1)

## Regras eliminatorias e operacionais

- [ ] Protocolo de troca de mensagens entregue antes da avaliacao
- [ ] Codigo fonte enviado ate o dia da avaliacao
- [ ] Servidor com campo/parametro de porta configuravel
- [ ] Cliente com campos de IP e porta do servidor
- [ ] Cliente apto a trocar mensagens com qualquer servidor
- [ ] Servidor apto a trocar mensagens com qualquer cliente
- [ ] Nenhuma alteracao no codigo no momento da avaliacao

## Evidencias recomendadas

- [ ] Colecao de requisicoes (Postman/Insomnia ou arquivo .http)
- [ ] Capturas de resposta de cada endpoint da EP-1
- [ ] README com instrucoes de execucao (cliente e servidor)
- [ ] Banco migrado e pronto para demonstracao

## Sequencia curta para demonstrar em sala

1. Definir IP/porta no cliente
2. Cadastrar usuario
3. Fazer login e obter token
4. Consultar usuario por ID
5. Atualizar usuario
6. Consultar novamente
7. Excluir/desativar usuario
8. Fazer logout
