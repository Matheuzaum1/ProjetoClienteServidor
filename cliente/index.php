<?php
$baseUrl = isset($_GET['baseUrl']) ? trim($_GET['baseUrl']) : 'http://127.0.0.1:8080';
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
                <input id="baseUrl" value="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>" placeholder="http://127.0.0.1:8080">
                <button id="saveBaseUrl" type="button">Salvar</button>
            </div>
            <small>Exemplo: http://192.168.0.10:8080</small>
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
        <pre id="output">Aguardando requisicoes...</pre>
    </section>
</main>

<script>
    window.__BASE_URL__ = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="assets/app.js"></script>
</body>
</html>