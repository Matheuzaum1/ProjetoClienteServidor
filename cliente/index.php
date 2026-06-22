<?php
$baseUrl = isset($_GET['baseUrl']) ? trim($_GET['baseUrl']) : 'http://127.0.0.1:25000';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cliente EP3 - Testes</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<div class="app-container">
    <header class="header">
        <div class="header-content">
            <div class="logo">📸 <span>Cliente EP-3</span></div>
            <nav style="display:flex;gap:8px;align-items:center;">
                <a href="home" class="btn-icon" title="Ver feed de posts">📰 Feed</a>
                <div class="server-config">
                    <input id="serverIp" type="text" placeholder="IP (ex: 127.0.0.1)" class="server-input" value="127.0.0.1" style="width:120px">
                    <input id="serverPort" type="text" placeholder="Porta" class="server-input" value="25000" style="width:70px">
                    <button id="saveBaseUrl" type="button" class="btn-icon" title="Salvar servidor">💾</button>
                    <button id="testConnection" type="button" class="btn-icon" title="Testar conexão">🔗</button>
                </div>
            </nav>
        </div>
    </header>

    <div class="user-bar" id="userBar">
        <div class="user-avatar" id="userAvatar">U</div>
        <div class="user-info">
            <strong id="userName">Carregando...</strong>
            <small id="userDetail">@usuario • ID: -</small>
        </div>
        <button class="btn-logout-small" id="panelLogoutBtn">Sair</button>
    </div>

    <div class="quick-panel">
        <button id="quickListUsersBtn" class="btn-primary">Listar usuários <span style="font-size:11px;opacity:0.7">(Item 6)</span></button>
        <button id="quickListLogadosBtn" class="btn-secondary">Usuários logados <span style="font-size:11px;opacity:0.7">(Item 10)</span></button>
        <pre id="quickListUsersOutput">Clique em um botão para ver o resultado.</pre>
    </div>

    <main class="main-content">
        <h2 class="section-title">Autenticação</h2>
        <div class="card-grid">
            <div class="card">
                <div class="card-header">📝 Cadastro</div>
                <form id="registerForm" class="card-form">
                    <input name="nome" placeholder="Nome completo" required>
                    <input name="usuario" placeholder="@usuario" required>
                    <input name="email" placeholder="email@exemplo.com" type="email" required>
                    <input name="senha" placeholder="Senha" type="password" required>
                    <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                    <input name="foto" placeholder="URL da foto (opcional)">
                    <button type="submit" class="btn-primary">Cadastrar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">🔐 Login</div>
                <form id="loginForm" class="card-form">
                    <input name="usuario" placeholder="@usuario" required>
                    <input name="senha" placeholder="Senha" type="password" required>
                    <button type="submit" class="btn-primary">Entrar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">🚪 Logout</div>
                <form id="logoutForm" class="card-form">
                    <button type="submit" class="btn-secondary">Sair</button>
                </form>
            </div>
        </div>

        <h2 class="section-title">Usuários <span class="item-badge">Item 6</span></h2>
        <div class="card-grid">
            <div class="card">
                <div class="card-header">🔍 Consultar Perfil</div>
                <form id="showForm" class="card-form">
                    <input name="id" placeholder="ID do usuário" required>
                    <button type="submit" class="btn-primary">Buscar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">✏️ Editar Perfil</div>
                <form id="updateForm" class="card-form">
                    <input name="id" placeholder="ID do usuário" required>
                    <input name="nome" placeholder="Nome completo" required>
                    <input name="usuario" placeholder="@usuario" required>
                    <input name="email" placeholder="email@exemplo.com" type="email" required>
                    <input name="senha" placeholder="Nova senha (opcional)" type="password">
                    <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                    <input name="foto" placeholder="URL da foto (opcional)">
                    <button type="submit" class="btn-primary">Salvar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">🗑️ Excluir Conta</div>
                <form id="deleteForm" class="card-form">
                    <input name="id" placeholder="ID do usuário" required>
                    <button type="submit" class="btn-danger">Excluir</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">📋 Listar Usuários <span class="item-badge">Item 6</span></div>
                <form id="listUsersForm" class="card-form">
                    <button type="submit" class="btn-primary">Buscar</button>
                </form>
                <div style="padding:0 16px 16px">
                    <div class="label">Resultado</div>
                    <pre id="listUsersOutput">Clique em Buscar para listar.</pre>
                </div>
            </div>
            <div class="card">
                <div class="card-header">🟢 Usuários Logados <span class="item-badge">Item 10</span></div>
                <form id="listLogadosForm" class="card-form">
                    <button type="submit" class="btn-primary">Buscar logados</button>
                </form>
                <div style="padding:0 16px 16px">
                    <div class="label">Usuários online</div>
                    <pre id="listLogadosOutput">Clique em Buscar para ver logados.</pre>
                </div>
            </div>
            <div class="card">
                <div class="card-header">✏️ Editar Outro Usuário</div>
                <form id="editOtherForm" class="card-form">
                    <input name="id" placeholder="ID do usuário" required>
                    <input name="nome" placeholder="Nome completo" required>
                    <input name="usuario" placeholder="@usuario" required>
                    <input name="email" placeholder="email@exemplo.com" type="email" required>
                    <input name="senha" placeholder="Nova senha (opcional)" type="password">
                    <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                    <input name="foto" placeholder="URL da foto (opcional)">
                    <button type="submit" class="btn-primary">Salvar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">🗑️ Deletar Outro Usuário</div>
                <form id="deleteOtherForm" class="card-form">
                    <input name="id" placeholder="ID do usuário" required>
                    <button type="submit" class="btn-danger">Deletar</button>
                </form>
            </div>
        </div>

        <h2 class="section-title">Posts <span class="item-badge">Itens 2,3,4,5,7,8,9</span></h2>
        <div class="card-grid">
            <div class="card">
                <div class="card-header">📸 Criar Post <span class="item-badge">Item 2</span></div>
                <form id="createPostForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário (autor)" required>
                    <textarea name="imagem" placeholder="Imagem (Base64 ou URL)" rows="2" required></textarea>
                    <input name="legenda" placeholder="Legenda (min 5 caracteres)" required>
                    <button type="submit" class="btn-primary">Publicar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">🔍 Consultar Post <span class="item-badge">Item 3</span></div>
                <form id="getPostForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário (autor)" required>
                    <input name="idPost" placeholder="ID do post" required>
                    <button type="submit" class="btn-primary">Buscar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">✏️ Atualizar Post <span class="item-badge">Item 4</span></div>
                <form id="updatePostForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário (autor)" required>
                    <input name="idPost" placeholder="ID do post" required>
                    <input name="legenda" placeholder="Nova legenda (min 5 caracteres)" required>
                    <button type="submit" class="btn-primary">Atualizar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">🗑️ Deletar Post <span class="item-badge">Item 5</span></div>
                <form id="deletePostForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário (autor)" required>
                    <input name="idPost" placeholder="ID do post" required>
                    <button type="submit" class="btn-danger">Deletar</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">📋 Listar Posts <span class="item-badge">Item 7</span></div>
                <form id="listPostsForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário" required>
                    <button type="submit" class="btn-primary">Listar</button>
                </form>
                <div style="padding:0 16px 16px">
                    <div class="label">Postagens</div>
                    <pre id="listPostsOutput">Clique em Listar para ver as postagens.</pre>
                </div>
            </div>
            <div class="card">
                <div class="card-header">❤️ Curtir Post <span class="item-badge">Item 8</span></div>
                <form id="likePostForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário (autor do post)" required>
                    <input name="idPost" placeholder="ID do post" required>
                    <button type="submit" class="btn-primary">Curtir</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header">💔 Descurtir Post <span class="item-badge">Item 9</span></div>
                <form id="unlikePostForm" class="card-form">
                    <input name="idUsuario" placeholder="ID do usuário (autor do post)" required>
                    <input name="idPost" placeholder="ID do post" required>
                    <button type="submit" class="btn-secondary">Descurtir</button>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Resposta</h3>
                <button id="clearOutput" class="btn-icon" title="Limpar">✕</button>
            </div>
            <div class="panel-body">
                <div class="panel-section">
                    <div class="label">Aviso Amigável</div>
                    <pre id="friendlyOutput">Aguardando requisições...</pre>
                </div>
                <div class="panel-section">
                    <div class="label">JSON Bruto</div>
                    <pre id="rawOutput">Aguardando requisições...</pre>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Logs</h3>
                <div style="display:flex;gap:8px">
                    <button id="refreshClientLogs" class="btn-icon" title="Atualizar">🔄</button>
                    <button id="clearClientLogs" class="btn-icon" title="Limpar">✕</button>
                </div>
            </div>
            <pre id="clientLogs">Nenhum log salvo ainda.</pre>
        </div>
    </main>
</div>

<script>
    window.__BASE_URL__ = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="assets/app.js"></script>
</body>
</html>
