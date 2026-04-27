Projeto: Instagram
Linguagem e framework : PHP & Laravel
O projeto deve usar REST

Requisitos Funcionais e Não Funcionais

Requisitos Funcionais (RF)
O que o sistema deve fazer e quais funcionalidades deve oferecer.
Cadastro de Usuários:
RF01 - Cadastro: O sistema deve permitir cadastro de novos usuários.
Campos obrigatórios: Nome,(@)Usuário, Senha, Email;
O sistema deve permitir que seja desativada a conta, não excluída.
	Autenticação (Login)
RF02 - Login: O sistema deve aceitar o login apenas com o preenchimento dos campos usuário e senha.
Ambos preenchidos obrigatoriamente e o sistema deve validar as credenciais
O autenticação deve ser Token JWT.
A autenticação do token será enviado o token no formato bearer token.
No token será enviado um ‘iat’ com o tempo de expiração do token 
RF03 - Logout: O sistema deve realizar o logout assim quando desejar.
	Seguir/Seguidores
RF04 - O usuário deve poder seguir e deixar de seguir outros usuários.
RF05 - Um usuário não pode seguir a si mesmo.
RF06 - O perfil de cada usuário deve exibir o número de seguidores e de usuários que ele segue.
RF07 - O feed deve exibir somente postagens dos usuários que o usuário autenticado segue.
RF08 - O usuário deve poder visualizar a lista de seus seguidores e de quem ele segue.
Perfil 
RF09 - O usuário deve poder editar seu perfil: nome completo, nome de usuário, foto de perfil e biografia. Biografia: máximo 150 caracteres. 
RF10 - O usuário deve poder visualizar o perfil público de outros usuários. 


Requisitos Não Funcionais (RNF)
Como o sistema deve operar e quais restrições tecnológicas deve respeitar.
RNF01 - Comunicação via HTTP: A comunicação entre as aplicações (Cliente e Servidor) deve ser construída exclusivamente por meio da troca de mensagens via protocolo HTTP.
RFN02 -  Campos do cadastro:O formulário deverá contar com a obrigatoriedade de todos os campos.
Campo Nome: O campo nome completo deve ter entre 3 e 60 caracteres.
Permitidos: Letras e espaços. Não permitido: caracteres especiais e números;
Campo Biografia: O campo Biografia deve ter no máximo 150 caracteres.
Permitidos: 
Campo Usuário: O campo usuário deve permitir entre 3 e 30 caracteres.
Permitir letras minúsculas, underline(_) e números. Não permitir caracteres especiais, espaço, letras maiúsculas;
 Campo E-mail: O campo e-mail deve permitir apenas com o formato (xxx@xxxx.com) entre 10 até 35 caracteres;
Campo Foto: A partir da segunda entrega; decidir como serão armazenadas ou referenciadas
 Campo Senha: O campo de senha deve permitir entre 8 e 24 caracteres no máximo.
Permitir apenas números e letras no campo deve ser oculto. Ex.:(****)
RNF03 - Cadastro de novas fotos (itens obrigatórios): O sistema deverá obrigatoriamente aceitar apenas novos post de imagem com formato apenas em .JPG com a obrigatoriedade de uma foto anexada com o limite de 1(uma) foto de no máximo 5MB e opcionalmente com uma descrição com limite de até 50 caracteres. 
RNF04 - Formato do token - Bearer token no cabeçalho da requisição
Postagens
RNF05 - O sistema obrigatoriamente a postagem de uma (1) foto, sendo obrigatório a legenda entre 5 a 200 caracteres, letras, espaços e não permitir caracteres especiais e acento;
RNF06 - Os formatos aceitos devem ser JPG, JPEG e PNG.
Aceitar apenas no tamanho máximo de 10 Mb.
RNF07 - O usuário pode editar a postagem apenas da legenda.
RNF08 - O usuário pode excluir a postagem mas não remover apenas a foto ou legenda.
Curtidas
RNF09 - O usuário pode curtir e descurtir uma postagem apenas 1 vez.
RNF10 - O sistema deverá exibir a quantidade de curtidas da postagem
RNF11 --O sistema não deverá permitir que um usuário não autenticado curta uma postagem.
Comentários
RNF12 - O usuário autenticado pode comentar em postagens.
O comentário deve ter entre 1 e 300 caracteres;
RNF13 - O usuário pode excluir seus próprios comentários.
RNF14 - O dono da postagem pode excluir qualquer comentário feito em sua publicação.


link swaggerhub
https://app.swaggerhub.com/apis-docs/allvesleooorganizati/instagram-api-gerenciamento-de-usuarios/1.0.0?view=uiDocs