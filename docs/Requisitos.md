Projeto: Instagram
Linguagem e framework: PHP & Laravel
Protocolo: REST HTTP
Autenticação: JWT Bearer Token

---

## 📋 Escopo por Entrega

### ✅ EP-1 (Entrega Parcial 1) - VIGENTE

Funcionalidades implementadas nesta entrega:

#### Requisitos Funcionais (RF) - EP-1

**RF01 - Cadastro de Usuários:**
- O sistema deve permitir cadastro de novos usuários
- Campos obrigatórios: Nome, @Usuário, Senha, Email
- O sistema deve permitir desativação de conta (soft-delete)

**RF02 - Autenticação (Login):**
- Login com usuário e senha obrigatórios
- Validação de credenciais
- Autenticação via JWT Bearer Token
- Token com informação de expiração (iat)

**RF03 - Logout:**
- O sistema deve permitir logout sob demanda

#### Requisitos Não-Funcionais (RNF) - EP-1

**RNF01 - Comunicação HTTP:**
- Comunicação exclusiva via protocolo HTTP (REST)
- Troca de mensagens JSON

**RNF02 - Validação de Campos:**
- Nome: 3-60 caracteres, letras e espaços, sem caracteres especiais/números
- Usuário: 3-30 caracteres, minúsculas, números, underline (_)
- Email: formato válido (xxx@xxxx.com), 10-35 caracteres
- Senha: 8-24 caracteres, números e letras, oculta
- Biografia (opcional): máximo 150 caracteres
- Foto (opcional): a definir a partir de EP-2

**RNF04 - Formato do Token:**
- Bearer token no cabeçalho `Authorization: Bearer <token>`

---

### 🔮 EP-2+ (Futuras Entregas) - NÃO IMPLEMENTADO

Os seguintes itens fazem parte do escopo completo do projeto mas **não estão incluídos em EP-1**:

#### RF04-RF10: Seguir/Seguidores, Feed, Perfil
- (RF04) Seguir/deixar de seguir usuários
- (RF05) Validação de autofollow
- (RF06) Exibir contagem de seguidores
- (RF07) Feed filtrando apenas seguidores
- (RF08) Listar seguidores/seguindo
- (RF09) Editar perfil (nome, foto, biografia)
- (RF10) Visualizar perfil público de outro usuário

#### RNF03-RNF14: Postagens, Curtidas, Comentários
- (RNF03-RNF08) Postagens de fotos com validação
- (RNF09-RNF11) Sistema de curtidas
- (RNF12-RNF14) Sistema de comentários

---

## 📡 Endpoints Implementados (EP-1)

| Método | Rota | Autenticação | RF | Descrição |
|--------|------|--------------|----|----|
| `POST` | `/usuarios` | ❌ | RF01 | Cadastro de usuário |
| `POST` | `/usuarios/login` | ❌ | RF02 | Login (retorna JWT) |
| `GET` | `/usuarios/{id}` | ✅ JWT | RF02 | Obter dados do usuário |
| `PATCH` | `/usuarios/{id}` | ✅ JWT | RF09 | Atualizar dados do usuário |
| `DELETE` | `/usuarios/{id}` | ✅ JWT | RF01 | Desativar/apagar usuário |
| `POST` | `/usuarios/logout` | ✅ JWT | RF03 | Logout |

---

## 📚 Referências

- **Protocolo Detalhado:** [EP1-Protocolo-Mensagens.md](EP1-Protocolo-Mensagens.md)
- **Rubrica de Avaliação:** [EP1-Checklist-Avaliacao.md](EP1-Checklist-Avaliacao.md)
- **Testes:** [EP1-Testes.http](EP1-Testes.http)
- **SwaggerHub:** https://app.swaggerhub.com/apis-docs/allvesleooorganizati/instagram-api-gerenciamento-de-usuarios/1.0.0?view=uiDocs
