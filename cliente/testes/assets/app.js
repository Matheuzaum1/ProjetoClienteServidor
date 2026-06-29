const friendlyOutput = document.getElementById('friendlyOutput');
const rawOutput = document.getElementById('rawOutput');
const serverIp = document.getElementById('serverIp');
const serverPort = document.getElementById('serverPort');
const saveBaseUrlButton = document.getElementById('saveBaseUrl');
const testConnectionButton = document.getElementById('testConnection');
const clearOutputButton = document.getElementById('clearOutput');
const clientLogsOutput = document.getElementById('clientLogs');
const refreshClientLogsButton = document.getElementById('refreshClientLogs');
const clearClientLogsButton = document.getElementById('clearClientLogs');
const listUsersOutput = document.getElementById('listUsersOutput');
const quickListUsersBtn = document.getElementById('quickListUsersBtn');
const quickListUsersOutput = document.getElementById('quickListUsersOutput');
const quickListLogadosBtn = document.getElementById('quickListLogadosBtn');
const listPostsOutput = document.getElementById('listPostsOutput');
const listLogadosOutput = document.getElementById('listLogadosOutput');
const loadingOverlay = document.getElementById('loadingOverlay');
const userBar = document.getElementById('userBar');
const userAvatar = document.getElementById('userAvatar');
const userName = document.getElementById('userName');
const userDetail = document.getElementById('userDetail');
const panelLogoutBtn = document.getElementById('panelLogoutBtn');

const tokenStorageKey = 'ep1_jwt_token';
const userStorageKey = 'ep1_user_data';
const baseUrlStorageKey = 'ep1_api_base_url';
const clientLogStorageKey = 'ep1_client_logs';

let loadingCount = 0;

function showToast(message, type = 'info', duration = 4000) {
    const container = document.querySelector('.toast-container') || (() => {
        const el = document.createElement('div');
        el.className = 'toast-container';
        document.body.appendChild(el);
        return el;
    })();

    const icons = { success: '✓', error: '✗', warning: '⚠', info: 'ℹ' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const iconSpan = document.createElement('span');
    iconSpan.className = 'toast-icon';
    iconSpan.textContent = icons[type] || 'ℹ';

    const msgSpan = document.createElement('span');
    msgSpan.className = 'toast-msg';
    msgSpan.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.className = 'toast-close';
    closeBtn.textContent = '✕';
    closeBtn.addEventListener('click', () => dismissToast(toast));

    toast.appendChild(iconSpan);
    toast.appendChild(msgSpan);
    toast.appendChild(closeBtn);
    container.appendChild(toast);

    if (duration > 0) {
        setTimeout(() => dismissToast(toast), duration);
    }
}

function dismissToast(toast) {
    if (toast.classList.contains('toast-out')) return;
    toast.classList.add('toast-out');
    setTimeout(() => toast.remove(), 260);
}

function showLoading() {
    loadingCount++;
    loadingOverlay.classList.add('active');
}

function hideLoading() {
    loadingCount--;
    if (loadingCount <= 0) {
        loadingCount = 0;
        loadingOverlay.classList.remove('active');
    }
}

function loadUserInfo() {
    const token = localStorage.getItem(tokenStorageKey);
    const userData = localStorage.getItem(userStorageKey);

    if (token && userData) {
        try {
            const user = JSON.parse(userData);
            userBar.classList.add('active');
            userAvatar.textContent = (user.nome || user.usuario || 'U').charAt(0).toUpperCase();
            userName.textContent = user.nome || user.usuario;
            userDetail.textContent = `@${user.usuario} • ID: ${user.id} • ${user.tipo_usuario || 'comum'}`;
            return user;
        } catch {
            userBar.classList.remove('active');
        }
    } else {
        userBar.classList.remove('active');
    }
    return null;
}

function formatFriendlyMessage(result) {
    if (!result) return 'Nenhuma resposta recebida.';
    if (typeof result === 'string') return result;
    if (result.erro) return `Erro: ${result.erro}`;

    const data = result.data || {};
    const status = data.status || (result.ok ? 'sucesso' : 'erro');

    if (!result.ok && data.errors && typeof data.errors === 'object') {
        const campos = Object.entries(data.errors)
            .map(([campo, msgs]) => `  • ${campo}: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
            .join('\n');
        return `Atenção: ${data.mensagem || 'Erro de validação'}\nCampos:\n${campos}`;
    }

    if (!result.ok && result.status === 422) {
        const msg = data.mensagem || 'Dados inválidos';
        if (data.errors) {
            const campos = Object.entries(data.errors)
                .map(([campo, msgs]) => `  • ${campo}: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                .join('\n');
            return `Atenção: ${msg}\nCampos:\n${campos}`;
        }
        return `Atenção: ${msg}`;
    }

    return `${status === 'sucesso' ? 'Sucesso' : 'Atenção'}: ${data.mensagem || 'Resposta recebida.'}`;
}

function setOutput(result) {
    const rawValue = typeof result === 'string' ? result : JSON.stringify(result, null, 2);
    friendlyOutput.textContent = formatFriendlyMessage(result);
    rawOutput.textContent = rawValue;

    if (result && !result.erro) {
        const data = result.data || {};
        const status = data.status || (result.ok ? 'sucesso' : 'erro');
        const mensagem = data.mensagem || 'Resposta recebida.';
        if (result.ok) {
            showToast(mensagem, 'success');
        } else if (result.status === 422 || result.status === 400) {
            showToast(mensagem, 'warning');
        } else if (result.status >= 500) {
            showToast(mensagem, 'error');
        }
    } else if (result && result.erro) {
        showToast(result.erro, 'error');
    }
}

function readClientLogs() {
    try {
        return JSON.parse(localStorage.getItem(clientLogStorageKey) || '[]');
    } catch {
        return [];
    }
}

function writeClientLogs(entries) {
    localStorage.setItem(clientLogStorageKey, JSON.stringify(entries.slice(-50)));
}

function appendClientLog(level, message, details = {}) {
    const entries = readClientLogs();
    entries.push({ timestamp: new Date().toISOString(), level, message, details });
    writeClientLogs(entries);
    renderClientLogs();
}

function renderClientLogs() {
    const entries = readClientLogs();
    if (!entries.length) {
        clientLogsOutput.textContent = 'Nenhum log salvo ainda.';
        return;
    }
    clientLogsOutput.textContent = entries
        .map(e => {
            const det = Object.keys(e.details || {}).length ? ` ${JSON.stringify(e.details)}` : '';
            return `[${e.timestamp}] ${e.level.toUpperCase()} - ${e.message}${det}`;
        })
        .join('\n');
}

function getBaseUrl() {
    return (localStorage.getItem(baseUrlStorageKey) || window.__BASE_URL__ || '').replace(/\/$/, '');
}

function saveBaseUrl(value) {
    localStorage.setItem(baseUrlStorageKey, value.replace(/\/$/, ''));
    if (serverIp && serverPort) {
        const match = value.match(/https?:\/\/([^:]+)(?::(\d+))?/);
        if (match) {
            serverIp.value = match[1];
            if (match[2]) serverPort.value = match[2];
        }
    }
}

function buildRequestCandidates(path) {
    const base = getBaseUrl();
    const p = path.startsWith('/') ? path : `/${path}`;
    if (!base) return [p];
    if (/\/api$/i.test(base)) return [`${base}${p}`];
    return [`${base}${p}`, `${base}/api${p}`];
}

function shouldAttachAuthHeader(path, method) {
    const p = path.startsWith('/') ? path : `/${path}`;
    const m = (method || 'GET').toUpperCase();
    if (p === '/usuarios' && m === 'POST') return false;
    if (p === '/usuarios/login' && m === 'POST') return false;
    return true;
}

async function request(path, options = {}) {
    showLoading();
    const headers = new Headers(options.headers || {});
    if (!headers.has('Accept')) headers.set('Accept', 'application/json');
    if (!headers.has('Content-Type') && options.body) headers.set('Content-Type', 'application/json');

    const token = localStorage.getItem(tokenStorageKey);
    if (token && !headers.has('Authorization') && shouldAttachAuthHeader(path, options.method)) {
        headers.set('Authorization', `Bearer ${token}`);
    }

    const candidates = buildRequestCandidates(path);
    let response, data, usedUrl, lastError;

    for (let i = 0; i < candidates.length; i++) {
        const url = candidates[i];
        const hasNext = i < candidates.length - 1;
        try {
            const r = await fetch(url, { ...options, headers });
            const text = await r.text();
            const parsed = text ? JSON.parse(text) : {};
            if (r.status === 404 && hasNext) {
                appendClientLog('warn', 'Endpoint nao encontrado, tentando rota alternativa', { url });
                continue;
            }
            response = r;
            data = parsed;
            usedUrl = url;
            break;
        } catch (error) {
            lastError = error;
            if (!hasNext) throw error;
        }
    }

    if (!response) throw lastError || new Error('Falha ao executar requisicao.');

    if (response.ok && data && data.dados && data.dados.token) {
        localStorage.setItem(tokenStorageKey, data.dados.token);
    }

    if (response.status === 401 && !options._retry) {
        try {
            const refreshResult = await request('/token/refresh', { method: 'POST', _retry: true });
            if (refreshResult && refreshResult.ok && refreshResult.data && refreshResult.data.dados && refreshResult.data.dados.token) {
                headers.set('Authorization', `Bearer ${refreshResult.data.dados.token}`);
                hideLoading();
                return await request(path, { ...options, _retry: true });
            }
        } catch (e) {
            console.warn('Refresh failed', e);
        }
    }

    if (!response.ok && data && data.codigo === 'token_invalido') {
        localStorage.removeItem(tokenStorageKey);
    }

    const result = { status: response.status, ok: response.ok, data };
    appendClientLog(response.ok ? 'info' : 'warn', `Requisição para ${path}`, {
        method: options.method || 'GET', status: response.status, url: usedUrl,
    });

    hideLoading();
    return result;
}

async function testServerConnection() {
    showLoading();
    const base = getBaseUrl();
    const candidates = /\/api$/i.test(base) ? [`${base}/up`] : [`${base}/up`, `${base}/api/up`];

    let response, usedUrl;
    for (let i = 0; i < candidates.length; i++) {
        const url = candidates[i];
        const hasNext = i < candidates.length - 1;
        try {
            const r = await fetch(url, { headers: { Accept: 'application/json, text/plain, */*' } });
            if (r.ok || !hasNext) { response = r; usedUrl = url; break; }
        } catch (error) { if (!hasNext) throw error; }
    }

    if (!response || !response.ok) {
        const status = response ? response.status : 'sem resposta';
        appendClientLog('error', 'Falha ao testar conexão', { baseUrl: getBaseUrl(), status });
        hideLoading();
        throw new Error(`Servidor respondeu com status ${status}.`);
    }

    appendClientLog('info', 'Conexão validada', { baseUrl: getBaseUrl(), url: usedUrl });
    hideLoading();
    return { ok: true, data: { status: 'sucesso', codigo: 'CONEXAO_OK', mensagem: `Conexão estabelecida em ${getBaseUrl()}.` } };
}

function formToJson(form) {
    return Object.fromEntries(new FormData(form).entries());
}

function formatPostsList(posts) {
    if (!posts || !posts.length) return 'Nenhum post encontrado.';
    return posts.map(p => `[ID: ${p.id}] Curtidas: ${p.curtidas} | Legenda: ${p.legenda}`).join('\n');
}

function formatLogadosList(usuarios) {
    if (!usuarios || !usuarios.length) return 'Nenhum usuário logado.';
    return usuarios.map(u => `${u.id} | @${u.usuario} | ${u.nome} | IP: ${u.ip || 'N/A'} | Desde: ${u.logged_in_at || 'N/A'}`).join('\n');
}

function formatAllPostsList(posts) {
    if (!posts || !posts.length) return 'Nenhum post encontrado.';
    return posts.map(p => {
        const author = p.usuario || {};
        return `[ID: ${p.id}] @${author.usuario || '?'} | Curtidas: ${p.curtidas} | ${p.legenda}`;
    }).join('\n');
}

function formatUsersList(result) {
    if (!result.ok || !result.data || !result.data.dados || !Array.isArray(result.data.dados.usuarios)) {
        return 'A listagem não retornou usuários.';
    }
    const lines = result.data.dados.usuarios.map(u => {
        const base = `${u.id} | @${u.usuario} | ${u.nome || '-'}`;
        return u.email ? `${base} | ${u.email}` : base;
    });
    return lines.length ? lines.join('\n') : 'Nenhum usuário encontrado.';
}

// ===== EVENT HANDLERS =====

document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        setOutput(await request('/usuarios', { method: 'POST', body: JSON.stringify(formToJson(e.currentTarget)) }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

async function doAutoLogin(usuario, senha) {
    try {
        const result = await request('/usuarios/login', { method: 'POST', body: JSON.stringify({ usuario, senha }) });
        setOutput(result);
        if (result.ok && result.data && result.data.dados) {
            const d = result.data.dados;
            if (d.refresh_token) localStorage.setItem('ep1_fallback_refresh', d.refresh_token);
            if (d.usuario) {
                localStorage.setItem(userStorageKey, JSON.stringify(d.usuario));
                loadUserInfo();
                autoFillUserId();
            }
        }
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
}

function autoFillUserId() {
    const userData = localStorage.getItem(userStorageKey);
    if (userData) {
        try {
            const user = JSON.parse(userData);
            const idUsuarioInput = document.querySelector('#createPostForm input[name="idUsuario"]');
            if (idUsuarioInput) idUsuarioInput.value = user.id;
            const listIdInput = document.querySelector('#listPostsForm input[name="idUsuario"]');
            if (listIdInput) listIdInput.value = user.id;
        } catch {}
    }
}

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const result = await request('/usuarios/login', { method: 'POST', body: JSON.stringify(formToJson(e.currentTarget)) });
        setOutput(result);
        if (result.ok && result.data && result.data.dados) {
            const d = result.data.dados;
            if (d.refresh_token) localStorage.setItem('ep1_fallback_refresh', d.refresh_token);
            if (d.usuario) {
                localStorage.setItem(userStorageKey, JSON.stringify(d.usuario));
                loadUserInfo();
                autoFillUserId();
            }
        }
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

const adminLoginBtn = document.getElementById('adminLoginBtn');
if (adminLoginBtn) {
    adminLoginBtn.addEventListener('click', () => doAutoLogin('admin', 'admin123'));
}

const user1LoginBtn = document.getElementById('user1LoginBtn');
if (user1LoginBtn) {
    user1LoginBtn.addEventListener('click', () => doAutoLogin('user1', 'senha123'));
}

document.getElementById('showForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { id } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'GET' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('updateForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const p = formToJson(e.currentTarget);
        const { id, ...body } = p;
        setOutput(await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'PATCH', body: JSON.stringify(body) }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('deleteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { id } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'DELETE' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('logoutForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const token = localStorage.getItem(tokenStorageKey);
    if (!token) {
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);
        setOutput({ status: 200, ok: true, data: { status: 'sucesso', mensagem: 'Logout realizado (sem token local).' } });
        loadUserInfo();
        return;
    }
    try {
        const refreshToken = localStorage.getItem('ep1_fallback_refresh');
        const body = refreshToken ? JSON.stringify({ refresh_token: refreshToken }) : '{}';
        const result = await request('/usuarios/logout', { method: 'POST', body });
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);
        localStorage.removeItem('ep1_fallback_refresh');
        setOutput(result);
        loadUserInfo();
    } catch (error) {
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);
        localStorage.removeItem('ep1_fallback_refresh');
        setOutput({ erro: error.message });
        hideLoading();
        loadUserInfo();
    }
});

if (panelLogoutBtn) {
    panelLogoutBtn.addEventListener('click', async () => {
        const token = localStorage.getItem(tokenStorageKey);
        if (token) {
            try {
                const refreshToken = localStorage.getItem('ep1_fallback_refresh');
                await request('/usuarios/logout', { method: 'POST', body: refreshToken ? JSON.stringify({ refresh_token: refreshToken }) : '{}' });
            } catch {}
        }
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);
        localStorage.removeItem('ep1_fallback_refresh');
        loadUserInfo();
        setOutput({ ok: true, data: { status: 'sucesso', mensagem: 'Logout realizado.' } });
    });
}

// Posts
document.getElementById('createPostForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const p = formToJson(e.currentTarget);
        const { idUsuario, ...body } = p;
        setOutput(await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts`, { method: 'POST', body: JSON.stringify(body) }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('getPostForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { idUsuario, idPost } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts/${encodeURIComponent(idPost)}`, { method: 'GET' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('updatePostForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const p = formToJson(e.currentTarget);
        const { idUsuario, idPost, ...body } = p;
        setOutput(await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts/${encodeURIComponent(idPost)}`, { method: 'PATCH', body: JSON.stringify(body) }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('deletePostForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { idUsuario, idPost } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts/${encodeURIComponent(idPost)}`, { method: 'DELETE' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('listPostsForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { idUsuario } = formToJson(e.currentTarget);
        const result = await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts`, { method: 'GET' });
        setOutput(result);
        if (listPostsOutput) {
            if (result.ok && result.data && result.data.posts) {
                listPostsOutput.textContent = formatPostsList(result.data.posts);
            } else if (result.data && result.data.mensagem) {
                listPostsOutput.textContent = result.data.mensagem;
            } else {
                listPostsOutput.textContent = 'Nenhum post encontrado.';
            }
        }
    } catch (error) {
        setOutput({ erro: error.message });
        if (listPostsOutput) listPostsOutput.textContent = `Erro: ${error.message}`;
        hideLoading();
    }
});

const listAllPostsBtn = document.getElementById('listAllPostsBtn');
if (listAllPostsBtn) {
    listAllPostsBtn.addEventListener('click', async () => {
        try {
            const result = await request('/posts', { method: 'GET' });
            setOutput(result);
            const listAllOutput = document.getElementById('listAllPostsOutput');
            if (listAllOutput) {
                if (result.ok && result.data && result.data.posts) {
                    listAllOutput.textContent = formatAllPostsList(result.data.posts);
                } else if (result.data && result.data.mensagem) {
                    listAllOutput.textContent = result.data.mensagem;
                } else {
                    listAllOutput.textContent = 'Nenhum post encontrado.';
                }
            }
        } catch (error) {
            setOutput({ erro: error.message });
            const listAllOutput = document.getElementById('listAllPostsOutput');
            if (listAllOutput) listAllOutput.textContent = `Erro: ${error.message}`;
            hideLoading();
        }
    });
}

const loadUsersWithPostsBtn = document.getElementById('loadUsersWithPostsBtn');
if (loadUsersWithPostsBtn) {
    loadUsersWithPostsBtn.addEventListener('click', async () => {
        try {
            const result = await request('/usuarios-com-posts', { method: 'GET' });
            setOutput(result);
            const usersOutput = document.getElementById('usersWithPostsOutput');
            if (usersOutput) {
                if (result.ok && result.data && result.data.usuarios) {
                    const lines = result.data.usuarios.map(u =>
                        `${u.id} | @${u.usuario} | ${u.nome || '-'}`
                    );
                    usersOutput.textContent = lines.length ? lines.join('\n') : 'Nenhum usuário com posts.';
                } else if (result.data && result.data.mensagem) {
                    usersOutput.textContent = result.data.mensagem;
                } else {
                    usersOutput.textContent = 'Nenhum usuário com posts.';
                }
            }
            populateUsersWithPostsSelect(result);
        } catch (error) {
            setOutput({ erro: error.message });
            const usersOutput = document.getElementById('usersWithPostsOutput');
            if (usersOutput) usersOutput.textContent = `Erro: ${error.message}`;
            hideLoading();
        }
    });
}

async function populateUsersWithPostsSelect(result) {
    const select = document.getElementById('listPostsUserSelect');
    if (!select) return;
    let users = [];
    if (result && result.ok && result.data && result.data.usuarios) {
        users = result.data.usuarios;
    } else {
        const r = await request('/usuarios-com-posts', { method: 'GET' });
        if (r.ok && r.data && r.data.usuarios) users = r.data.usuarios;
    }
    select.innerHTML = '<option value="">-- Selecione um usuário --</option>';
    users.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = `@${u.usuario} (ID: ${u.id}) - ${u.nome || ''}`;
        select.appendChild(opt);
    });
    select.addEventListener('change', () => {
        const idInput = document.getElementById('listPostsUserId');
        if (idInput) idInput.value = select.value;
    });
}

document.getElementById('likePostForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { idUsuario, idPost } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts/${encodeURIComponent(idPost)}`, { method: 'POST' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('unlikePostForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { idUsuario, idPost } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(idUsuario)}/posts/${encodeURIComponent(idPost)}/curtir`, { method: 'DELETE' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

// Users & Admin
document.getElementById('listUsersForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const result = await request('/usuarios', { method: 'GET' });
        setOutput(result);
        if (listUsersOutput) listUsersOutput.textContent = formatUsersList(result);
    } catch (error) {
        setOutput({ erro: error.message });
        if (listUsersOutput) listUsersOutput.textContent = `Erro: ${error.message}`;
        hideLoading();
    }
});

if (quickListUsersBtn) {
    quickListUsersBtn.addEventListener('click', async () => {
        try {
            const result = await request('/usuarios', { method: 'GET' });
            setOutput(result);
            if (quickListUsersOutput) quickListUsersOutput.textContent = formatUsersList(result);
            if (listUsersOutput) listUsersOutput.textContent = formatUsersList(result);
        } catch (error) {
            setOutput({ erro: error.message });
            if (quickListUsersOutput) quickListUsersOutput.textContent = `Erro: ${error.message}`;
            hideLoading();
        }
    });
}

if (quickListLogadosBtn) {
    quickListLogadosBtn.addEventListener('click', async () => {
        try {
            const result = await request('/usuarios/logados', { method: 'GET' });
            setOutput(result);
            if (quickListUsersOutput) {
                if (result.ok && result.data && result.data.dados && result.data.dados.usuarios) {
                    quickListUsersOutput.textContent = formatLogadosList(result.data.dados.usuarios);
                } else if (result.data && result.data.mensagem) {
                    quickListUsersOutput.textContent = result.data.mensagem;
                } else {
                    quickListUsersOutput.textContent = 'Nenhum usuário logado.';
                }
            }
        } catch (error) {
            setOutput({ erro: error.message });
            if (quickListUsersOutput) quickListUsersOutput.textContent = `Erro: ${error.message}`;
            hideLoading();
        }
    });
}

document.getElementById('listLogadosForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const result = await request('/usuarios/logados', { method: 'GET' });
        setOutput(result);
        if (listLogadosOutput) {
            if (result.ok && result.data && result.data.dados && result.data.dados.usuarios) {
                listLogadosOutput.textContent = formatLogadosList(result.data.dados.usuarios);
            } else if (result.data && result.data.mensagem) {
                listLogadosOutput.textContent = result.data.mensagem;
            } else {
                listLogadosOutput.textContent = 'Nenhum usuário logado.';
            }
        }
    } catch (error) {
        setOutput({ erro: error.message });
        if (listLogadosOutput) listLogadosOutput.textContent = `Erro: ${error.message}`;
        hideLoading();
    }
});

document.getElementById('editOtherForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const p = formToJson(e.currentTarget);
        const { id, ...body } = p;
        setOutput(await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'PATCH', body: JSON.stringify(body) }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

document.getElementById('deleteOtherForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const { id } = formToJson(e.currentTarget);
        setOutput(await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'DELETE' }));
    } catch (error) { setOutput({ erro: error.message }); hideLoading(); }
});

// UI Setup
saveBaseUrlButton.addEventListener('click', () => {
    const ip = serverIp.value.trim() || '127.0.0.1';
    const port = serverPort.value.trim() || '25000';
    saveBaseUrl(`http://${ip}:${port}`);
    setOutput({ ok: true, data: { status: 'sucesso', mensagem: `Servidor definido como ${getBaseUrl()}` } });
    appendClientLog('info', 'Servidor configurado', { baseUrl: getBaseUrl() });
});

testConnectionButton.addEventListener('click', async () => {
    try {
        const ip = serverIp.value.trim() || '127.0.0.1';
        const port = serverPort.value.trim() || '25000';
        saveBaseUrl(`http://${ip}:${port}`);
        setOutput(await testServerConnection());
    } catch (error) {
        setOutput({ erro: `Não foi possível conectar: ${error.message}` });
    }
});

clearOutputButton.addEventListener('click', () => setOutput('Aguardando requisicoes...'));
refreshClientLogsButton.addEventListener('click', renderClientLogs);
clearClientLogsButton.addEventListener('click', () => { localStorage.removeItem(clientLogStorageKey); renderClientLogs(); });

// Init
loadUserInfo();
autoFillUserId();
setOutput({ ok: true, data: { status: 'sucesso', mensagem: 'Cliente EP-3 pronto. Configure o servidor.' } });
appendClientLog('info', 'Cliente carregado', { baseUrl: getBaseUrl() });
renderClientLogs();
populateUsersWithPostsSelect(null);
