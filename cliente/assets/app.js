const friendlyOutput = document.getElementById('friendlyOutput');
const rawOutput = document.getElementById('rawOutput');
const baseUrlInput = document.getElementById('baseUrl');
const saveBaseUrlButton = document.getElementById('saveBaseUrl');
const clearOutputButton = document.getElementById('clearOutput');

const tokenStorageKey = 'ep1_jwt_token';
const baseUrlStorageKey = 'ep1_api_base_url';

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

function getBaseUrl() {
    return (localStorage.getItem(baseUrlStorageKey) || window.__BASE_URL__ || '').replace(/\/$/, '');
}

function saveBaseUrl(value) {
    localStorage.setItem(baseUrlStorageKey, value.replace(/\/$/, ''));
    baseUrlInput.value = value.replace(/\/$/, '');
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
    if (token && !headers.has('Authorization')) {
        headers.set('Authorization', `Bearer ${token}`);
    }

    const response = await fetch(`${getBaseUrl()}${path}`, {
        ...options,
        headers,
    });

    const text = await response.text();
    let data;
    try {
        data = text ? JSON.parse(text) : {};
    } catch {
        data = text;
    }

    if (response.ok && data && data.dados && data.dados.token) {
        localStorage.setItem(tokenStorageKey, data.dados.token);
    }

    return { status: response.status, ok: response.ok, data };
}

function formToJson(form) {
    return Object.fromEntries(new FormData(form).entries());
}

document.getElementById('registerForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const result = await request('/api/usuarios', {
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
        const result = await request('/api/usuarios/login', {
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
        const result = await request(`/api/usuarios/${encodeURIComponent(id)}`, { method: 'GET' });
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
        const result = await request(`/api/usuarios/${encodeURIComponent(id)}`, {
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
        const result = await request(`/api/usuarios/${encodeURIComponent(id)}`, { method: 'DELETE' });
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

document.getElementById('logoutForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const result = await request('/api/usuarios/logout', { method: 'POST', body: '{}' });
        localStorage.removeItem(tokenStorageKey);
        setOutput(result);
    } catch (error) {
        setOutput({ erro: error.message });
    }
});

saveBaseUrlButton.addEventListener('click', () => {
    saveBaseUrl(baseUrlInput.value.trim());
    setOutput({ ok: true, data: { status: 'sucesso', mensagem: `Servidor definido como ${getBaseUrl()}` } });
});

clearOutputButton.addEventListener('click', () => setOutput('Aguardando requisicoes...'));

baseUrlInput.value = getBaseUrl();
setOutput({ ok: true, data: { status: 'sucesso', mensagem: 'Cliente pronto. Configure o servidor e execute uma operacao.' } });