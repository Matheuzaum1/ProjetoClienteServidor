<?php
$baseUrl = isset($_GET['baseUrl']) ? trim($_GET['baseUrl']) : 'http://127.0.0.1:25000';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cliente EP2 - Instagram</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="app-container">
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="4" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                </svg>
                <span>Instagram</span>
            </div>
            <div class="server-config" style="display: flex; gap: 5px; align-items: center;">
                <input id="serverIp" type="text" placeholder="IP (ex: 127.0.0.1)" class="server-input" style="width: 140px;">
                <input id="serverPort" type="text" placeholder="Porta (ex: 25000)" class="server-input" style="width: 80px;">
                <button id="saveBaseUrl" type="button" class="btn-icon" title="Salvar servidor">💾</button>
                <button id="testConnection" type="button" class="btn-icon" title="Testar conexão">🔗</button>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <nav class="nav-menu">
                <button class="nav-item active" data-tab="feed">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-label">Feed</span>
                </button>
                <button class="nav-item" data-tab="explore">
                    <span class="nav-icon">🔍</span>
                    <span class="nav-label">Explorar</span>
                </button>
                <button class="nav-item" data-tab="messages">
                    <span class="nav-icon">💬</span>
                    <span class="nav-label">Mensagens</span>
                </button>
                <button class="nav-item" data-tab="profile">
                    <span class="nav-icon">👤</span>
                    <span class="nav-label">Perfil</span>
                </button>
            </nav>
            <div class="sidebar-footer">
                <button class="nav-item" data-tab="settings">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-label">Configurações</span>
                </button>
            </div>
        </aside>

        <!-- Feed Central -->
        <main class="feed">
            <!-- Feed Tab -->
            <section class="tab-content active" data-tab="feed">
                <div class="feed-grid">
                    <!-- Post: Cadastro -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">📝</div>
                            <div class="post-info">
                                <h3>Cadastro</h3>
                                <small>Criar nova conta</small>
                            </div>
                        </div>
                        <form id="registerForm" class="post-form">
                            <input name="nome" placeholder="Nome completo" required>
                            <input name="usuario" placeholder="@usuario" required>
                            <input name="email" placeholder="email@exemplo.com" type="email" required>
                            <input name="senha" placeholder="Senha" type="password" required>
                            <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                            <input name="foto" placeholder="URL da foto (opcional)">
                            <button type="submit" class="btn-primary">Cadastrar</button>
                        </form>
                    </article>

                    <!-- Post: Login -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">🔐</div>
                            <div class="post-info">
                                <h3>Login</h3>
                                <small>Entrar na conta</small>
                            </div>
                        </div>
                        <form id="loginForm" class="post-form">
                            <input name="usuario" placeholder="@usuario" required>
                            <input name="senha" placeholder="Senha" type="password" required>
                            <button type="submit" class="btn-primary">Entrar</button>
                        </form>
                    </article>

                    <!-- Post: Consulta -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">🔍</div>
                            <div class="post-info">
                                <h3>Consultar Perfil</h3>
                                <small>Ver dados de usuário</small>
                            </div>
                        </div>
                        <form id="showForm" class="post-form">
                            <input name="id" placeholder="ID do usuário" required>
                            <button type="submit" class="btn-primary">Buscar</button>
                        </form>
                    </article>

                    <!-- Post: Atualizar -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">✏️</div>
                            <div class="post-info">
                                <h3>Editar Perfil</h3>
                                <small>Atualizar informações</small>
                            </div>
                        </div>
                        <form id="updateForm" class="post-form">
                            <input name="id" placeholder="ID do usuário" required>
                            <input name="nome" placeholder="Nome completo" required>
                            <input name="usuario" placeholder="@usuario" required>
                            <input name="email" placeholder="email@exemplo.com" type="email" required>
                            <input name="senha" placeholder="Nova senha (opcional)" type="password">
                            <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                            <input name="foto" placeholder="URL da foto (opcional)">
                            <button type="submit" class="btn-primary">Salvar</button>
                        </form>
                    </article>

                    <!-- Post: Excluir -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">🗑️</div>
                            <div class="post-info">
                                <h3>Excluir Conta</h3>
                                <small>Desativar usuário</small>
                            </div>
                        </div>
                        <form id="deleteForm" class="post-form">
                            <input name="id" placeholder="ID do usuário" required>
                            <button type="submit" class="btn-danger">Excluir</button>
                        </form>
                    </article>

                    <!-- Post: Logout -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">🚪</div>
                            <div class="post-info">
                                <h3>Logout</h3>
                                <small>Sair da conta</small>
                            </div>
                        </div>
                        <form id="logoutForm" class="post-form">
                            <button type="submit" class="btn-secondary">Sair</button>
                        </form>
                    </article>
                </div>
            </section>

            <!-- Explore Tab -->
            <section class="tab-content" data-tab="explore">
                <div class="section-title">�‍💼 Operações ADM</div>
                <div class="feed-grid">
                    <!-- Post: Listar Usuários -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">📋</div>
                            <div class="post-info">
                                <h3>Listar Usuários</h3>
                                <small>Ver todos os usuários</small>
                            </div>
                        </div>
                        <form id="listUsersForm" class="post-form">
                            <button type="submit" class="btn-primary">Buscar</button>
                        </form>
                    </article>

                    <!-- Post: Editar Outro Usuário -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">✏️</div>
                            <div class="post-info">
                                <h3>Editar Outro Usuário</h3>
                                <small>Atualizar dados de outro perfil</small>
                            </div>
                        </div>
                        <form id="editOtherForm" class="post-form">
                            <input name="id" placeholder="ID do usuário" required>
                            <input name="nome" placeholder="Nome completo" required>
                            <input name="usuario" placeholder="@usuario" required>
                            <input name="email" placeholder="email@exemplo.com" type="email" required>
                            <input name="senha" placeholder="Nova senha (opcional)" type="password">
                            <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                            <input name="foto" placeholder="URL da foto (opcional)">
                            <button type="submit" class="btn-primary">Salvar</button>
                        </form>
                    </article>

                    <!-- Post: Deletar Outro Usuário -->
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">🗑️</div>
                            <div class="post-info">
                                <h3>Deletar Outro Usuário</h3>
                                <small>Remover perfil de outro usuário</small>
                            </div>
                        </div>
                        <form id="deleteOtherForm" class="post-form">
                            <input name="id" placeholder="ID do usuário" required>
                            <button type="submit" class="btn-danger">Deletar</button>
                        </form>
                    </article>
                </div>
            </section>

            <!-- Messages Tab -->
            <section class="tab-content" data-tab="messages">
                <div class="section-title">💬 Mensagens</div>
                <div class="empty-state">
                    <p>Nenhuma mensagem por enquanto</p>
                </div>
            </section>

            <!-- Profile Tab -->
            <section class="tab-content" data-tab="profile">
                <div class="section-title">👤 Perfil</div>
                <div class="profile-placeholder">
                    <div class="avatar-large">👤</div>
                    <h2>Seu Perfil</h2>
                    <p>Faça login para ver suas informações</p>
                </div>
            </section>

            <!-- Settings Tab -->
            <section class="tab-content" data-tab="settings">
                <div class="section-title">⚙️ Configurações</div>
                <div class="settings-panel">
                    <div class="setting-item">
                        <label>Servidor API</label>
                        <input id="baseUrlSettings" class="setting-input">
                        <button class="btn-secondary" id="saveBaseUrlSettings">Salvar</button>
                    </div>
                </div>
            </section>

            <!-- Response Panel -->
            <section class="response-panel">
                <div class="panel-header">
                    <h3>Resposta</h3>
                    <button id="clearOutput" class="btn-icon" title="Limpar">✕</button>
                </div>
                <div class="response-content">
                    <div class="response-item">
                        <div class="response-label">Aviso Amigável</div>
                        <pre id="friendlyOutput">Aguardando requisições...</pre>
                    </div>
                    <div class="response-item">
                        <div class="response-label">JSON Bruto</div>
                        <pre id="rawOutput">Aguardando requisições...</pre>
                    </div>
                </div>
            </section>

            <!-- Logs Panel -->
            <section class="logs-panel">
                <div class="panel-header">
                    <h3>Logs</h3>
                    <div class="panel-actions">
                        <button id="refreshClientLogs" class="btn-icon" title="Atualizar">🔄</button>
                        <button id="clearClientLogs" class="btn-icon" title="Limpar">✕</button>
                    </div>
                </div>
                <pre id="clientLogs">Nenhum log salvo ainda.</pre>
            </section>
        </main>

        <!-- Right Sidebar (Stories/Suggestions) -->
        <aside class="right-sidebar">
            <div class="stories">
                <h3>Stories</h3>
                <div class="stories-grid">
                    <div class="story">📸</div>
                    <div class="story">📹</div>
                    <div class="story">🎬</div>
                </div>
            </div>
            <div class="suggestions">
                <h3>Sugestões</h3>
                <div class="suggestion-item">
                    <div class="suggestion-avatar">👥</div>
                    <div class="suggestion-info">
                        <div>Novo Usuário</div>
                        <small>Sugerido para você</small>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    window.__BASE_URL__ = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="assets/app.js"></script>
</body>
</html>