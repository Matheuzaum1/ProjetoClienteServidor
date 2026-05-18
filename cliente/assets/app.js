const friendlyOutput = document.getElementById('friendlyOutput');
const rawOutput = document.getElementById('rawOutput');
const baseUrlInput = document.getElementById('baseUrl');
const saveBaseUrlButton = document.getElementById('saveBaseUrl');
const testConnectionButton = document.getElementById('testConnection');
const clearOutputButton = document.getElementById('clearOutput');
const clientLogsOutput = document.getElementById('clientLogs');
const refreshClientLogsButton = document.getElementById('refreshClientLogs');
const clearClientLogsButton = document.getElementById('clearClientLogs');

const tokenStorageKey = 'ep1_jwt_token';
const baseUrlStorageKey = 'ep1_api_base_url';
const clientLogStorageKey = 'ep1_client_logs';

function formatFriendlyMessage(result) {
    if (!result) {
        return 'Nenhuma resposta recebida.';
    }

    if (typeof result === 'string') {
        return result;
    }

    if (result.erro) {
        return `Erro: ${result.erro}`;
    }

    const data = result.data || {};
    const status = data.status || (result.ok ? 'sucesso' : 'erro');
    const mensagem = data.mensagem || 'Resposta recebida.';

    return `${status === 'sucesso' ? 'Sucesso' : 'Atenção'}: ${mensagem}`;
}

function setOutput(result) {
    const rawValue = typeof result === 'string' ? result : JSON.stringify(result, null, 2);
    friendlyOutput.textContent = formatFriendlyMessage(result);
    rawOutput.textContent = rawValue;
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
    const entry = {
        timestamp: new Date().toISOString(),
        level,
        message,
        details,
    };

    const entries = readClientLogs();
    entries.push(entry);
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
        .map((entry) => {
            const detailsText = Object.keys(entry.details || {}).length ? ` ${JSON.stringify(entry.details)}` : '';
            return `[${entry.timestamp}] ${entry.level.toUpperCase()} - ${entry.message}${detailsText}`;
        })
        .join('\n');
}

function getBaseUrl() {
    return (localStorage.getItem(baseUrlStorageKey) || window.__BASE_URL__ || '').replace(/\/$/, '');
}

function saveBaseUrl(value) {
    localStorage.setItem(baseUrlStorageKey, value.replace(/\/$/, ''));
    baseUrlInput.value = value.replace(/\/$/, '');
}

function buildRequestCandidates(path) {
    const base = getBaseUrl();
    const sanitizedPath = path.startsWith('/') ? path : `/${path}`;

    if (!base) {
        return [sanitizedPath];
    }

    if (/\/api$/i.test(base)) {
        return [`${base}${sanitizedPath}`];
    }

    return [`${base}${sanitizedPath}`, `${base}/api${sanitizedPath}`];
}

function shouldAttachAuthHeader(path, method) {
    const sanitizedPath = path.startsWith('/') ? path : `/${path}`;
    const normalizedMethod = (method || 'GET').toUpperCase();

    if (sanitizedPath === '/usuarios' && normalizedMethod === 'POST') {
        return false;
    }

    if (sanitizedPath === '/usuarios/login' && normalizedMethod === 'POST') {
        return false;
    }

    return true;
}

async function request(path, options = {}) {
    const headers = new Headers(options.headers || {});
    if (!headers.has('Accept')) {
        headers.set('Accept', 'application/json');
    }
    if (!headers.has('Content-Type') && options.body) {
        headers.set('Content-Type', 'application/json');
    }

    const token = localStorage.getItem(tokenStorageKey);
    if (token && !headers.has('Authorization') && shouldAttachAuthHeader(path, options.method)) {
        headers.set('Authorization', `Bearer ${token}`);
    }

    const candidates = buildRequestCandidates(path);
    let response;
    let data;
    let usedUrl = '';
    let lastError;

    for (let index = 0; index < candidates.length; index += 1) {
        const candidateUrl = candidates[index];
        const hasNextCandidate = index < candidates.length - 1;

        try {
            const candidateResponse = await fetch(candidateUrl, {
                ...options,
                headers,
            });

            const text = await candidateResponse.text();
            let parsed;
            try {
                parsed = text ? JSON.parse(text) : {};
            } catch {
                parsed = text;
            }

            if (candidateResponse.status === 404 && hasNextCandidate) {
                appendClientLog('warn', 'Endpoint nao encontrado, tentando rota alternativa', {
                    url: candidateUrl,
                });
                continue;
            }

            response = candidateResponse;
            data = parsed;
            usedUrl = candidateUrl;
            break;
        } catch (error) {
            lastError = error;
            if (!hasNextCandidate) {
                throw error;
            }
        }
    }

    if (!response) {
        throw lastError || new Error('Falha ao executar requisicao.');
    }

    if (response.ok && data && data.dados && data.dados.token) {
        localStorage.setItem(tokenStorageKey, data.dados.token);
    }

    if (!response.ok && data && data.codigo === 'token_invalido') {
        localStorage.removeItem(tokenStorageKey);
    }

    const result = { status: response.status, ok: response.ok, data };
    appendClientLog(response.ok ? 'info' : 'warn', `Requisição para ${path}`, {
        method: options.method || 'GET',
        status: response.status,
        url: usedUrl,
    });

    return result;
}

async function testServerConnection() {
    const base = getBaseUrl();
    const candidates = /\/api$/i.test(base)
        ? [`${base}/up`]
        : [`${base}/up`, `${base}/api/up`];

    let response;
    let usedUrl = '';

    for (let index = 0; index < candidates.length; index += 1) {
        const url = candidates[index];
        const hasNextCandidate = index < candidates.length - 1;

        try {
            const candidateResponse = await fetch(url, {
                headers: {
                    Accept: 'application/json, text/plain, */*',
                },
            });

            if (candidateResponse.ok || !hasNextCandidate) {
                response = candidateResponse;
                usedUrl = url;
                break;
            }
        } catch (error) {
            if (!hasNextCandidate) {
                throw error;
            }
        }
    }

    if (!response || !response.ok) {
        const status = response ? response.status : 'sem resposta';
        appendClientLog('error', 'Falha ao testar conexão com o servidor', {
            baseUrl: getBaseUrl(),
            status,
        });
        throw new Error(`Servidor respondeu com status ${status}.`);
    }

    appendClientLog('info', 'Conexão com o servidor validada', {
        baseUrl: getBaseUrl(),
        url: usedUrl,
    });

    return {
        ok: true,
        data: {
            status: 'sucesso',
            codigo: 'CONEXAO_OK',
            mensagem: `Conexão com o servidor estabelecida em ${getBaseUrl()}.`,
        },
    };
}

function formToJson(form) {
    return Object.fromEntries(new FormData(form).entries());
}

document.getElementById('registerForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const result = await request('/usuarios', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('loginForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const result = await request('/usuarios/login', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('showForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const { id } = formToJson(event.currentTarget);
        const result = await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'GET' });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('updateForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const { id, ...body } = payload;
        const result = await request(`/usuarios/${encodeURIComponent(id)}`, {
            method: 'PATCH',
            body: JSON.stringify(body),
        });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('deleteForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const { id } = formToJson(event.currentTarget);
        const result = await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'DELETE' });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('logoutForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const result = await request('/usuarios/logout', { method: 'POST', body: '{}' });
        localStorage.removeItem(tokenStorageKey);
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

// ADM Operations
document.getElementById('listUsersForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const result = await request('/usuarios', { method: 'GET' });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('editOtherForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const { id, ...body } = payload;
        const result = await request(`/usuarios/${encodeURIComponent(id)}`, {
            method: 'PATCH',
            body: JSON.stringify(body),
        });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('deleteOtherForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const { id } = formToJson(event.currentTarget);
        const result = await request(`/usuarios/${encodeURIComponent(id)}`, { method: 'DELETE' });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

saveBaseUrlButton.addEventListener('click', () => {
    saveBaseUrl(baseUrlInput.value.trim());
    setOutput({ ok: true, data: { status: 'sucesso', mensagem: `Servidor definido como ${getBaseUrl()}` } });
    appendClientLog('info', 'Servidor configurado no cliente', { baseUrl: getBaseUrl() });
});

testConnectionButton.addEventListener('click', async () => {
    try {
        saveBaseUrl(baseUrlInput.value.trim());
        const result = await testServerConnection();
        setOutput(result);
    } catch (error) {
        setOutput({ erro: `Não foi possível conectar ao servidor: ${error.message}` });
    }
});

clearOutputButton.addEventListener('click', () => setOutput('Aguardando requisicoes...'));

refreshClientLogsButton.addEventListener('click', renderClientLogs);

clearClientLogsButton.addEventListener('click', () => {
    localStorage.removeItem(clientLogStorageKey);
    renderClientLogs();
});

baseUrlInput.value = getBaseUrl();
setOutput({ ok: true, data: { status: 'sucesso', mensagem: 'Cliente pronto. Configure o servidor e execute uma operacao.' } });
appendClientLog('info', 'Cliente carregado', { baseUrl: getBaseUrl() });
renderClientLogs();

// Tab Navigation
const navItems = document.querySelectorAll('.nav-item');
const tabContents = document.querySelectorAll('.tab-content');

function switchTab(tabName) {
    // Remove active class from all nav items and tab contents
    navItems.forEach((item) => item.classList.remove('active'));
    tabContents.forEach((tab) => tab.classList.remove('active'));

    // Add active class to selected tab
    const activeNavItem = document.querySelector(`[data-tab="${tabName}"]`);
    const activeTabContent = document.querySelector(`.tab-content[data-tab="${tabName}"]`);

    if (activeNavItem) {
        activeNavItem.classList.add('active');
    }
    if (activeTabContent) {
        activeTabContent.classList.add('active');
    }

    // Scroll to top of feed
    document.querySelector('.feed').scrollTop = 0;
}

navItems.forEach((item) => {
    item.addEventListener('click', () => {
        const tabName = item.getAttribute('data-tab');
        switchTab(tabName);
    });
});

// Settings panel
const baseUrlSettingsInput = document.getElementById('baseUrlSettings');
const saveBaseUrlSettingsButton = document.getElementById('saveBaseUrlSettings');

if (baseUrlSettingsInput) {
    baseUrlSettingsInput.value = getBaseUrl();
}

if (saveBaseUrlSettingsButton) {
    saveBaseUrlSettingsButton.addEventListener('click', () => {
        if (baseUrlSettingsInput) {
            saveBaseUrl(baseUrlSettingsInput.value.trim());
            baseUrlInput.value = getBaseUrl();
            setOutput({ ok: true, data: { status: 'sucesso', mensagem: `Servidor definido como ${getBaseUrl()}` } });
            appendClientLog('info', 'Servidor reconfigurado nas configurações', { baseUrl: getBaseUrl() });
        }
    });
}
