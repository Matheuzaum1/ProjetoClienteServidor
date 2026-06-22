<?php
$configPath = __DIR__ . '/server-config.json';
$defaultUrl = 'http://127.0.0.1:25000';
if (file_exists($configPath)) {
    $cfg = json_decode(file_get_contents($configPath), true);
    if (isset($cfg['baseUrl'])) $defaultUrl = $cfg['baseUrl'];
}
$baseUrl = isset($_GET['baseUrl']) ? trim($_GET['baseUrl']) : $defaultUrl;
?><!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feed - Projeto Cliente/Servidor</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .post-actions { display: flex; gap: 8px; padding: 8px 16px 14px; border-top: 1px solid var(--border); }
        .btn-like, .btn-unlike {
            border: none; background: none; cursor: pointer; font-size: 22px; padding: 4px 6px;
            border-radius: 6px; transition: transform var(--transition), background var(--transition);
            display: inline-flex; align-items: center; gap: 4px;
        }
        .btn-like:hover { transform: scale(1.2); background: rgba(237,73,86,0.08); }
        .btn-unlike:hover { transform: scale(1.2); background: rgba(0,0,0,0.04); }
        .btn-like:active, .btn-unlike:active { transform: scale(0.95); }
        .btn-like .count, .btn-unlike .count { font-size: 13px; font-weight: 600; color: var(--text-secondary); }
        .post-time { font-size: 11px; color: var(--text-tertiary); margin-top: 2px; }

        .feed-controls {
            display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
            padding: 14px 16px; background: var(--bg-secondary);
            border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 16px;
        }
        .feed-controls .search-input {
            padding: 9px 12px; border: 1px solid var(--border); border-radius: 6px;
            font-size: 13px; background: var(--bg); color: var(--text); flex: 1; min-width: 140px;
            transition: border-color var(--transition), box-shadow var(--transition);
        }
        .feed-controls .search-input:focus {
            outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px rgba(0,149,246,0.15);
        }
        .feed-user-filter { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
        .feed-user-filter .user-chip {
            padding: 5px 12px; border: 1px solid var(--border); border-radius: 16px;
            background: var(--bg); cursor: pointer; font-size: 12px; font-weight: 500;
            transition: all var(--transition);
        }
        .feed-user-filter .user-chip:hover { background: var(--bg-tertiary); }
        .feed-user-filter .user-chip.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .feed-user-filter .user-chip .chip-avatar {
            display: inline-block; width: 20px; height: 20px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #00c6fb); color: #fff;
            text-align: center; line-height: 20px; font-size: 10px; font-weight: 700;
            margin-right: 4px; vertical-align: middle;
        }
        .feed-stats { font-size: 12px; color: var(--text-tertiary); margin-bottom: 12px; padding: 0 4px; }

        .login-prompt {
            text-align: center; padding: 40px 20px;
        }
        .login-prompt h2 { font-size: 20px; margin: 0 0 16px; }
        .login-prompt .login-form {
            display: flex; flex-direction: column; gap: 10px; max-width: 300px; margin: 0 auto;
        }
        .login-prompt .login-form input {
            padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px;
            font-size: 14px; background: var(--bg); color: var(--text);
            transition: border-color var(--transition);
        }
        .login-prompt .login-form input:focus {
            outline: none; border-color: var(--accent);
        }
        .login-prompt .login-error { color: var(--danger); font-size: 13px; margin-top: 8px; }

        @media (max-width: 768px) {
            .feed-controls { flex-direction: column; align-items: stretch; }
            .feed-user-filter { justify-content: center; }
        }
    </style>
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<div class="app-container" style="max-width:640px;">
    <header class="header" style="margin:0 -16px 16px;padding-left:16px;padding-right:16px;">
        <div class="header-content">
            <div class="logo">📸 <span>Feed</span></div>
            <nav style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <div class="server-config">
                    <input id="serverIp" type="text" placeholder="IP" class="server-input" value="127.0.0.1" style="width:100px">
                    <input id="serverPort" type="text" placeholder="Porta" class="server-input" value="25000" style="width:65px">
                    <button id="saveServerBtn" type="button" class="btn-icon" title="Salvar servidor">💾</button>
                </div>
                <a href="./" class="btn-icon" title="Painel de Testes">🧪</a>
                <button id="refreshFeedBtn" class="refresh-btn">🔄</button>
            </nav>
        </div>
    </header>

    <div class="user-bar" id="userBar">
        <div class="user-avatar" id="userAvatar">U</div>
        <div class="user-info">
            <strong id="userName">Carregando...</strong>
            <small id="userDetail">@usuario</small>
        </div>
        <button class="btn-logout-small" id="feedLogoutBtn">Sair</button>
    </div>

    <div class="feed-controls" id="feedControls">
        <input id="feedSearch" class="search-input" type="text" placeholder="Buscar na legenda..." autocomplete="off">
        <div class="feed-user-filter" id="userFilter">
            <button class="user-chip active" data-user-id="">Todos</button>
        </div>
    </div>

    <div class="feed-stats" id="feedStats"></div>

    <div id="feedContainer">
        <div class="feed-loading">Carregando feed...</div>
    </div>
</div>

<script>
const tokenStorageKey = 'ep1_jwt_token';
const baseUrlStorageKey = 'ep1_api_base_url';
const userStorageKey = 'ep1_user_data';
let allPosts = [];
let allUsers = [];
let loadingCount = 0;
let isLoggedIn = false;

function showLoading() {
    loadingCount++;
    document.getElementById('loadingOverlay').classList.add('active');
}
function hideLoading() {
    loadingCount--;
    if (loadingCount <= 0) { loadingCount = 0; document.getElementById('loadingOverlay').classList.remove('active'); }
}

function getBaseUrl() {
    return (localStorage.getItem(baseUrlStorageKey) || <?php echo json_encode($baseUrl); ?>).replace(/\/$/, '');
}
function saveBaseUrl(value) {
    localStorage.setItem(baseUrlStorageKey, value.replace(/\/$/, ''));
}

function updateLoginStatus() {
    const bar = document.getElementById('userBar');
    const avatar = document.getElementById('userAvatar');
    const name = document.getElementById('userName');
    const detail = document.getElementById('userDetail');
    const data = localStorage.getItem(userStorageKey);
    const token = localStorage.getItem(tokenStorageKey);
    isLoggedIn = !!(token && data);
    if (isLoggedIn) {
        try {
            const u = JSON.parse(data);
            bar.classList.add('active');
            avatar.textContent = (u.nome || u.usuario || 'U').charAt(0).toUpperCase();
            name.textContent = u.nome || u.usuario;
            detail.textContent = '@' + (u.usuario || '?') + ' (ID: ' + (u.id || '-') + ')';
            return u;
        } catch { isLoggedIn = false; }
    }
    bar.classList.remove('active');
    return null;
}

async function request(path, options) {
    showLoading();
    const headers = new Headers(options.headers || {});
    if (!headers.has('Accept')) headers.set('Accept', 'application/json');
    if (!headers.has('Content-Type') && options.body) headers.set('Content-Type', 'application/json');
    const token = localStorage.getItem(tokenStorageKey);
    const p = path.startsWith('/') ? path : '/' + path;
    if (token) headers.set('Authorization', 'Bearer ' + token);
    try {
        const r = await fetch(getBaseUrl() + p, { ...options, headers });
        const text = await r.text();
        const data = text ? JSON.parse(text) : {};
        if (r.ok && data && data.dados && data.dados.token) {
            localStorage.setItem(tokenStorageKey, data.dados.token);
            if (data.dados.usuario) {
                localStorage.setItem(userStorageKey, JSON.stringify(data.dados.usuario));
            }
        }
        return { status: r.status, ok: r.ok, data };
    } catch (e) {
        return { status: 0, ok: false, data: null, error: e.message };
    } finally {
        hideLoading();
    }
}

async function loadFeed() {
    const container = document.getElementById('feedContainer');
    const controls = document.getElementById('feedControls');
    container.innerHTML = '<div class="feed-loading">Carregando feed...</div>';

    const result = await request('/feed', { method: 'GET' });

    if (result.ok && result.data && result.data.posts) {
        allPosts = result.data.posts;
        controls.style.display = '';
        renderFeed();
        loadUsers();
        return;
    }

    controls.style.display = 'none';

    if (result.status === 401) {
        container.innerHTML =
            '<div class="login-prompt">'
            + '<h2>🔐 Faça login para ver o feed</h2>'
            + '<div class="login-form">'
            + '<input id="loginUser" type="text" placeholder="@usuario" value="admin">'
            + '<input id="loginPass" type="password" placeholder="Senha" value="admin">'
            + '<button id="loginBtn" class="btn-primary">Entrar</button>'
            + '<div id="loginError" class="login-error"></div>'
            + '</div>'
            + '</div>';
        document.getElementById('loginBtn').addEventListener('click', doLogin);
        for (const id of ['loginUser', 'loginPass']) {
            document.getElementById(id).addEventListener('keydown', e => {
                if (e.key === 'Enter') doLogin();
            });
        }
    } else {
        const msg = (result.data && result.data.mensagem) || (result.error ? 'Erro de conexao: ' + result.error : 'Erro ao carregar feed.');
        container.innerHTML = '<div class="feed-error">' + escHtml(msg) + '</div>';
    }
}

async function doLogin() {
    const user = document.getElementById('loginUser').value.trim();
    const pass = document.getElementById('loginPass').value.trim();
    const errEl = document.getElementById('loginError');
    if (!user || !pass) { errEl.textContent = 'Preencha usuario e senha.'; return; }
    errEl.textContent = '';
    document.getElementById('loginBtn').disabled = true;
    document.getElementById('loginBtn').textContent = 'Entrando...';

    const result = await request('/usuarios/login', { method: 'POST', body: JSON.stringify({ usuario: user, senha: pass }) });

    if (result.ok && result.data && result.data.dados && result.data.dados.token) {
        errEl.textContent = '';
        updateLoginStatus();
        loadFeed();
    } else {
        const msg = (result.data && result.data.mensagem) || 'Falha no login.';
        errEl.textContent = msg;
        document.getElementById('loginBtn').disabled = false;
        document.getElementById('loginBtn').textContent = 'Entrar';
    }
}

async function loadUsers() {
    const result = await request('/usuarios', { method: 'GET' });
    if (result.ok && result.data && result.data.dados && result.data.dados.usuarios) {
        allUsers = result.data.dados.usuarios;
        renderUserChips();
    }
}

function renderUserChips() {
    const container = document.getElementById('userFilter');
    const active = container.querySelector('.active');
    const currentId = active ? active.dataset.userId : '';
    container.innerHTML = '<button class="user-chip' + (currentId === '' ? ' active' : '') + '" data-user-id="">Todos</button>';
    allUsers.forEach(u => {
        const btn = document.createElement('button');
        btn.className = 'user-chip' + (String(u.id) === currentId ? ' active' : '');
        btn.dataset.userId = u.id;
        const initial = (u.nome || u.usuario || '?').charAt(0).toUpperCase();
        btn.innerHTML = '<span class="chip-avatar">' + initial + '</span>' + escHtml('@' + u.usuario);
        btn.addEventListener('click', () => {
            container.querySelectorAll('.user-chip').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            renderFeed();
        });
        container.appendChild(btn);
    });
}

function renderFeed() {
    const container = document.getElementById('feedContainer');
    const searchTerm = document.getElementById('feedSearch').value.trim().toLowerCase();
    const activeChip = document.querySelector('#userFilter .active');
    const filterUserId = activeChip ? activeChip.dataset.userId : '';
    updateLoginStatus();

    let filtered = allPosts;
    if (filterUserId) {
        filtered = filtered.filter(p => String(p.usuario.id) === filterUserId);
    }
    if (searchTerm) {
        filtered = filtered.filter(p => (p.legenda || '').toLowerCase().includes(searchTerm));
    }

    document.getElementById('feedStats').textContent = filtered.length + ' post' + (filtered.length !== 1 ? 's' : '') + ' encontrado' + (filtered.length !== 1 ? 's' : '');

    if (!filtered.length) {
        container.innerHTML = '<div class="feed-empty">📭 Nenhum post encontrado.</div>';
        return;
    }

    container.innerHTML = filtered.map(p => {
        const author = p.usuario || {};
        const authorName = author.nome || author.usuario || 'Desconhecido';
        const authorUser = author.usuario || '?';
        const initial = authorName.charAt(0).toUpperCase();
        const authorFoto = author.foto || '';
        const avatarStyle = authorFoto ? 'background:url(' + escHtml(authorFoto) + ') center/cover;' : '';
        const imgHtml = p.imagem && p.imagem.startsWith('data:')
            ? '<img class="feed-post-img" src="' + p.imagem + '" alt="Post" loading="lazy">'
            : '<div class="feed-post-img-placeholder">📷</div>';
        const timeHtml = p.created_at ? '<div class="post-time">' + new Date(p.created_at).toLocaleString('pt-BR') + '</div>' : '';
        const likeDisabled = !isLoggedIn ? ' disabled style="opacity:0.4;cursor:not-allowed;"' : '';

        return '<div class="feed-post" data-post-id="' + p.id + '" data-author-id="' + author.id + '">'
            + '<div class="feed-post-header">'
            + '<div class="feed-post-avatar" style="' + avatarStyle + '">' + (avatarStyle ? '' : initial) + '</div>'
            + '<div>'
            + '<div class="feed-post-user">' + escHtml(authorName) + ' <small>@' + escHtml(authorUser) + '</small></div>'
            + timeHtml
            + '</div>'
            + '</div>'
            + imgHtml
            + '<div class="feed-post-body">'
            + '<div class="feed-post-legenda">' + escHtml(p.legenda || '') + '</div>'
            + '</div>'
            + '<div class="post-actions">'
            + '<button class="btn-like" data-post-id="' + p.id + '" data-author-id="' + author.id + '"' + likeDisabled + ' title="Curtir">❤️ <span class="count">' + (p.curtidas || '0') + '</span></button>'
            + '<button class="btn-unlike" data-post-id="' + p.id + '" data-author-id="' + author.id + '"' + likeDisabled + ' title="Descurtir">💔</button>'
            + '</div>'
            + '</div>';
    }).join('');

    document.querySelectorAll('.btn-like').forEach(btn => btn.addEventListener('click', onLike));
    document.querySelectorAll('.btn-unlike').forEach(btn => btn.addEventListener('click', onUnlike));
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function showToast(message, type) {
    const container = document.querySelector('.toast-container') || (() => {
        const el = document.createElement('div'); el.className = 'toast-container'; document.body.appendChild(el); return el;
    })();
    const icons = { success: '✓', error: '✗', warning: '⚠', info: 'ℹ' };
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + (type || 'info');
    toast.innerHTML = '<span class="toast-icon">' + (icons[type] || 'ℹ') + '</span><span class="toast-msg">' + message + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'toast-out\');setTimeout(()=>this.parentElement.remove(),260)">✕</button>';
    container.appendChild(toast);
    setTimeout(() => { if (toast.classList) { toast.classList.add('toast-out'); setTimeout(() => toast.remove(), 260); } }, 3500);
}

async function onLike(e) {
    const btn = e.currentTarget;
    const postId = btn.dataset.postId;
    const authorId = btn.dataset.authorId;
    const result = await request('/usuarios/' + authorId + '/posts/' + postId, { method: 'POST' });
    if (result.ok) {
        showToast('Curtida adicionada!', 'success');
        loadFeed();
    } else {
        const msg = (result.data && result.data.mensagem) || 'Erro ao curtir';
        showToast(msg, result.status === 401 ? 'warning' : 'error');
        if (result.status === 401) loadFeed();
    }
}

async function onUnlike(e) {
    const btn = e.currentTarget;
    const postId = btn.dataset.postId;
    const authorId = btn.dataset.authorId;
    const result = await request('/usuarios/' + authorId + '/posts/' + postId + '/curtir', { method: 'DELETE' });
    if (result.ok) {
        showToast('Curtida removida!', 'success');
        loadFeed();
    } else {
        const msg = (result.data && result.data.mensagem) || 'Erro ao descurtir';
        showToast(msg, result.status === 401 ? 'warning' : 'error');
        if (result.status === 401) loadFeed();
    }
}

document.getElementById('feedLogoutBtn').addEventListener('click', () => {
    const token = localStorage.getItem(tokenStorageKey);
    if (token) {
        const refreshToken = localStorage.getItem('ep1_fallback_refresh');
        request('/usuarios/logout', { method: 'POST', body: refreshToken ? JSON.stringify({ refresh_token: refreshToken }) : '{}' }).catch(() => {});
    }
    localStorage.removeItem(tokenStorageKey);
    localStorage.removeItem(userStorageKey);
    localStorage.removeItem('ep1_fallback_refresh');
    updateLoginStatus();
    loadFeed();
    showToast('Logout realizado', 'info');
});

document.getElementById('saveServerBtn').addEventListener('click', () => {
    const ip = document.getElementById('serverIp').value.trim() || '127.0.0.1';
    const port = document.getElementById('serverPort').value.trim() || '25000';
    saveBaseUrl('http://' + ip + ':' + port);
    showToast('Servidor: ' + getBaseUrl(), 'info');
    loadFeed();
});

document.getElementById('refreshFeedBtn').addEventListener('click', () => {
    if (isLoggedIn) { loadFeed(); loadUsers(); }
    else { loadFeed(); }
});

let searchTimer;
document.getElementById('feedSearch').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(renderFeed, 250);
});

updateLoginStatus();
loadFeed();
</script>
</body>
</html>
