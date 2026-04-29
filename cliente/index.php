<?php
$baseUrl = isset($_GET['baseUrl']) ? trim($_GET['baseUrl']) : 'http://127.0.0.1:25000';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cliente EP1 - Instagram</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<main class="shell">
    <section class="hero">
        <div>
            <p class="eyebrow">EP1 Cliente/Servidor</p>
            <h1>Cliente REST para a API de usuarios</h1>
            <p class="lead">Informe o IP e a porta do servidor, conecte e execute cadastro, login, consulta, atualizacao, exclusao e logout sem sair da pagina.</p>
        </div>
        <div class="server-card">
            <label for="baseUrl">Servidor (IP + porta)</label>
            <div class="server-row">
                <input id="baseUrl" value="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>" placeholder="http://127.0.0.1:25000">
                <button id="saveBaseUrl" type="button">Salvar</button>
                <button id="testConnection" type="button" class="ghost">Testar conexão</button>
            </div>
            <small>Exemplo local: http://127.0.0.1:25000</small>
        </div>
    </section>

    <section class="grid">
        <article class="card">
            <h2>Cadastro</h2>
            <form id="registerForm" class="stack">
                <input name="nome" placeholder="Nome completo" required>
                <input name="usuario" placeholder="usuario" required>
                <input name="email" placeholder="email@exemplo.com" type="email" required>
                <input name="senha" placeholder="Senha" type="password" required>
                <input name="biografia" placeholder="Biografia opcional">
                <input name="foto" placeholder="URL da foto opcional">
                <button type="submit">Cadastrar</button>
            </form>
        </article>

        <article class="card">
            <h2>Login</h2>
            <form id="loginForm" class="stack">
                <input name="usuario" placeholder="usuario" required>
                <input name="senha" placeholder="Senha" type="password" required>
                <button type="submit">Entrar</button>
            </form>
        </article>

        <article class="card">
            <h2>Consulta</h2>
            <form id="showForm" class="stack">
                <input name="id" placeholder="ID do usuario" required>
                <button type="submit">Buscar</button>
            </form>
        </article>

        <article class="card">
            <h2>Atualizacao</h2>
            <form id="updateForm" class="stack">
                <input name="id" placeholder="ID do usuario" required>
                <input name="nome" placeholder="Nome completo" required>
                <input name="usuario" placeholder="usuario" required>
                <input name="email" placeholder="email@exemplo.com" type="email" required>
                <input name="senha" placeholder="Nova senha opcional" type="password">
                <input name="biografia" placeholder="Biografia opcional">
                <input name="foto" placeholder="URL da foto opcional">
                <button type="submit">Atualizar</button>
            </form>
        </article>

        <article class="card">
            <h2>Exclusao</h2>
            <form id="deleteForm" class="stack">
                <input name="id" placeholder="ID do usuario" required>
                <button type="submit" class="danger">Excluir</button>
            </form>
        </article>

        <article class="card">
            <h2>Logout</h2>
            <form id="logoutForm" class="stack">
                <button type="submit" class="ghost">Logout com token salvo</button>
            </form>
        </article>
    </section>

    <section class="card output-card">
        <div class="output-head">
            <h2>Resposta</h2>
            <button id="clearOutput" type="button" class="ghost">Limpar</button>
        </div>
        <div class="output-stack">
            <div>
                <p class="output-label">Aviso amigável</p>
                <pre id="friendlyOutput">Aguardando requisicoes...</pre>
            </div>
            <div>
                <p class="output-label">JSON bruto</p>
                <pre id="rawOutput">Aguardando requisicoes...</pre>
            </div>
        </div>
    </section>

    <section class="card output-card">
        <div class="output-head">
            <h2>Logs do Cliente</h2>
            <div class="output-actions">
                <button id="refreshClientLogs" type="button" class="ghost">Atualizar</button>
                <button id="clearClientLogs" type="button" class="ghost">Limpar logs</button>
            </div>
        </div>
        <div class="output-stack">
            <div>
                <p class="output-label">Últimas interações</p>
                <pre id="clientLogs">Nenhum log salvo ainda.</pre>
            </div>
        </div>
    </section>
</main>

<script>
    window.__BASE_URL__ = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="assets/app.js"></script>
</body>
</html>