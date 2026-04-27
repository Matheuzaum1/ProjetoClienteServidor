#Possuir pelo menos um módulo cliente e um módulo servi-
dor.
#Comunicação entre os módulos unicamente por troca de
mensagens por HTTP.
#Usar um protocolo de troca de mensagens definido em co-
mum acordo com os alunos
#Um cliente deverá ser capaz de se comunicar com todos os
servidores.
#Um servidor deverá ser capaz de se comunicar com todos os
clientes.
#Similaridade entre os códigos: aceitável até o limite de 50%.
#Não poderão ocorrer correções no código no momento da En-
trega Parcial.
#Cada arquivo enviado deve ser nomeado: “NNNNNNN-EPy.-
zip”, onde “NNNNNNN” representa o R.A. do aluno, e “y” o
número da entrega parcial. Por exemplo:
>1234567-EP2.zip, mostra que o aluno com o R.A. 1234567
enviou o arquivo da Entrega Parcial 2.
#Dentro do arquivo compactado enviado por cada aluno deve-
rá existir um arquivo README.txt na raiz da estrutura com a
explicação da ações necessárias para a execução correta do cli-
ente e do servidor do aluno
#Qualquer biblioteca utilizada que não faça parte da instalação
padrão do framework utilizado na construção do PF deverá ser
enviada dentro do arquivo enviado (p.ex., biblioteca JSON, bi-
blioteca DB, etc.).
Padronizações para o aplicativo Web
#REST:
>Localização do recurso: URL
>Ação a ser executada no recurso: método HTTP (GET, POST, DELETE,
PUT, …)
>Ex:
#http://www.escola.com/students/studentRollno/07
>Com GET, o serviço irá retornar as informações do estudante número
07 na lista.
>Com PUT, as informações do estudante podem ser atualizadas atra-
vés do envio dos dados de um formulário, p.ex.
#A mensagem genérica de qualquer pedido (request) ou resposta
(reply) HTTP consiste dos seguintes quatro itens (RFC 822):
1. Um linha inicial;
2. Zero ou mais cabeçalhos (headers);
3. Uma linha vazia indicando o fim dos campos do cabeçalho;
4. E (opcionalmente) o corpo (body) da mensagem.
#O cabeçalho HTTP contém o metadado e a informação sobre o mé-
todo HTTP, enquanto o corpo (body) contém o dado que se deseja
enviar ao servidor.
#O cabeçalho geralmente contém o hostname.
#Recursos (URIs) são representados como substantivos.
>P. ex.: /students
#Não se usa URLs para descrever ações.
>P. ex.: /students/add ou
> /students/getStudents não é RESTful
#Recursos são indicados sempre no plural. P. ex.: /students/1
#Em REST, o foco é em identificar os recursos, ao invés das ações.
#Redesenhando a interface para ser orientada a dados (tratando
uma sessão como recurso) ao invés de orientada a controle (tratan-
do um login, p.ex., como uma ação) é um princípio chave de REST.
# (/users/login) é considerado orientado a controle porque foca no
procedimento de se logar.
# (/sessions) identifica o recurso conceitual - sessão – que pode ser
nomeada e manipulada.
#Inserindo informações (CRUD):
>POST /students/1 (informação no corpo da mensagem)
>POST /courses/10 (informação no corpo da mensagem)
#Obtendo informações (CRUD):
>GET /students/
>GET /students/1
#Atualizando informações (CRUD):
>PUT ou PATCH /students/1 (informação no corpo da msg)
>PUT ou PATCH /courses/10 (informação no corpo da msg)
#Apagando informações (CRUD):
>DELETE /students/1
>DELETE /courses/10
#Login
>POST /sessions/
>(credenciais no corpo da mensagem)
#Logout
>POST, PUT ou DELETE /sessions/{id}
#Postagem de fotos:
>POST /posts: Cria uma nova postagem de foto.
#Postagem de comentários:
>POST /posts/{id}/comments: Cria um comentário como um re-
curso subordinado à uma postagem específica.
#Retornar um código apropriado para o cliente:
>https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
>https://www.iana.org/assignments/http-status-codes/http-sta-
tus-codes.xhtml
>https://www.iana.org/assignments/http-status-codes/http-sta-
tus-codes.txt
JSON Web Token (JWT)
#“JWT authentication: Best practices and when to use it”
#“JWT should not be your default for sessions”
#“How to implement JWT authentication with Vue and Node.js”
#“How to secure a REST API using JWT authentication”
JSON Web Token (JWT)
A questão de ‘logging out’:
# Com sessões tradicionais, pode-se só remover o token de sessão de seu armaze-
namento de sessão, que é efetivo o suficiente para invalidar a sessão.
# Com o JWT e outros tokens sem estado (stateless) isso não é possível. Não é possí-
vel remover o token, porque ele é auto contido e não existe autoridade central que
possa invalidá-los.
# Isso é resolvido tipicamente de 3 formas:
1) Os tokens devem ser feitos para não durarem muito. Por exemplo, 5 minutos. An-
tes que os 5 minutos terminem, é gerado um novo token.
2) Através de um sistema que contém a lista dos tokens recentemente expirados.
3) Partir do pressuposto que não existe logout controlado por servidor, assu-
mindo-se que o cliente pode apagar seus próprios tokens.