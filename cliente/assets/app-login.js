const tokenStorageKey = 'ep1_jwt_token';
const baseUrlStorageKey = 'ep1_api_base_url';
const userStorageKey = 'ep1_user_data';

// DOM Elements
const authLoginForm = document.getElementById('authLoginForm');
const authRegisterForm = document.getElementById('authRegisterForm');
const authMessage = document.getElementById('authMessage');
const toggleBtns = document.querySelectorAll('.toggle-btn');
const switchToRegister = document.getElementById('switchToRegister');
const switchToLogin = document.getElementById('switchToLogin');
const profileModal = document.getElementById('profileModal');
const modalOverlay = document.getElementById('modalOverlay');
const closeProfile = document.getElementById('closeProfile');
const editProfileBtn = document.getElementById('editProfileBtn');
const logoutBtn = document.getElementById('logoutBtn');
const editProfileForm = document.getElementById('editProfileForm');
const cancelEdit = document.getElementById('cancelEdit');

function getBaseUrl() {
    return (localStorage.getItem(baseUrlStorageKey) || 'http://127.0.0.1:25000').replace(/\/$/, '');
}

function saveBaseUrl(value) {
    localStorage.setItem(baseUrlStorageKey, value.replace(/\/$/, ''));
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

    for (let index = 0; index < candidates.length; index += 1) {
        const candidateUrl = candidates[index];
        const hasNextCandidate = index < candidates.length - 1;

        try {
                const candidateResponse = await fetch(candidateUrl, {
                    ...options,
                    headers,
                    // include credentials so HttpOnly refresh cookie is sent
                    credentials: 'include',
                });

            const text = await candidateResponse.text();
            let parsed;
            try {
                parsed = text ? JSON.parse(text) : {};
            } catch {
                parsed = text;
            }

            if (candidateResponse.status === 404 && hasNextCandidate) {
                continue;
            }

            response = candidateResponse;
            data = parsed;
            break;
        } catch (error) {
            if (!hasNextCandidate) {
                throw error;
            }
        }
    }

    if (!response) {
        throw new Error('Falha ao executar requisição.');
    }

    // Handle access token in response (login/refresh)
    if (response.ok && data && data.dados && data.dados.token) {
        localStorage.setItem(tokenStorageKey, data.dados.token);
    }

    // refresh_token is now stored in an HttpOnly cookie by the server; no localStorage handling here.

    // If unauthorized because access token expired, try refresh once
    if (response.status === 401 && !options._retry) {
        try {
            const refreshResult = await request('/token/refresh', { method: 'POST', _retry: true });
            if (refreshResult && refreshResult.ok && refreshResult.data && refreshResult.data.dados && refreshResult.data.dados.token) {
                const newToken = refreshResult.data.dados.token;
                localStorage.setItem(tokenStorageKey, newToken);
                if (!headers.has('Authorization') && shouldAttachAuthHeader(path, options.method)) {
                    headers.set('Authorization', `Bearer ${newToken}`);
                }
                return await request(path, { ...options, _retry: true });
            }
        } catch (e) {
            console.warn('Refresh attempt failed', e);
        }
    }

    if (!response.ok && data && data.codigo === 'token_invalido') {
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(refreshStorageKey);
    }

    return { status: response.status, ok: response.ok, data };
}

function showMessage(text, isError = false) {
    authMessage.textContent = text;
    authMessage.className = `auth-message ${isError ? 'error' : 'success'}`;
    authMessage.style.display = 'block';

    if (!isError) {
        setTimeout(() => {
            authMessage.style.display = 'none';
        }, 3000);
    }
}

function switchForm(formName) {
    authLoginForm.classList.remove('active');
    authRegisterForm.classList.remove('active');
    authMessage.style.display = 'none';

    if (formName === 'login') {
        authLoginForm.classList.add('active');
        toggleBtns[0].classList.add('active');
        toggleBtns[1].classList.remove('active');
    } else {
        authRegisterForm.classList.add('active');
        toggleBtns[0].classList.remove('active');
        toggleBtns[1].classList.add('active');
    }
}

function formToJson(form) {
    return Object.fromEntries(new FormData(form).entries());
}

function showProfile(user) {
    document.getElementById('profileName').textContent = user.nome || user.usuario;
    document.getElementById('profileUser').textContent = `@${user.usuario}`;
    document.getElementById('profileEmail').textContent = user.email;
    document.getElementById('profileBio').textContent = user.biografia || 'Sem biografia';
    document.getElementById('editUserId').value = user.id;

    if (user.foto_url) {
        document.getElementById('profilePhoto').src = user.foto_url;
    }

    // Populate edit form
    document.querySelector('#editProfileForm input[name="nome"]').value = user.nome || '';
    document.querySelector('#editProfileForm input[name="usuario"]').value = user.usuario || '';
    document.querySelector('#editProfileForm input[name="email"]').value = user.email || '';
    document.querySelector('#editProfileForm textarea[name="biografia"]').value = user.biografia || '';
    document.querySelector('#editProfileForm input[name="foto"]').value = user.foto_url || '';

    authLoginForm.parentElement.style.display = 'none';
    authRegisterForm.parentElement.style.display = 'none';
    profileModal.classList.add('show');
    modalOverlay.style.display = 'flex';
}

function hideProfile() {
    profileModal.classList.remove('show');
    modalOverlay.style.display = 'none';
    authLoginForm.parentElement.style.display = 'block';
    authRegisterForm.parentElement.style.display = 'block';
    editProfileForm.style.display = 'none';
}

function checkLogin() {
    const token = localStorage.getItem(tokenStorageKey);
    const userData = localStorage.getItem(userStorageKey);

    if (token && userData) {
        try {
            const user = JSON.parse(userData);
            showProfile(user);
        } catch {
            localStorage.removeItem(tokenStorageKey);
            localStorage.removeItem(userStorageKey);
        }
    }
}

// Event Listeners
toggleBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
        switchForm(btn.dataset.form);
    });
});

switchToRegister.addEventListener('click', (e) => {
    e.preventDefault();
    switchForm('register');
});

switchToLogin.addEventListener('click', (e) => {
    e.preventDefault();
    switchForm('login');
});

authLoginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const result = await request('/usuarios/login', {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        if (result.ok && result.data.dados.usuario) {
            localStorage.setItem(userStorageKey, JSON.stringify(result.data.dados.usuario));
            if (result.data.dados.refresh_token) {
                localStorage.setItem(refreshStorageKey, result.data.dados.refresh_token);
            }
            showMessage('Login realizado com sucesso!', false);
            setTimeout(() => {
                showProfile(result.data.dados.usuario);
            }, 500);
        } else {
            showMessage(result.data.mensagem || 'Erro ao fazer login', true);
        }
    } catch (error) {
        showMessage(error.message, true);
    }
});

authRegisterForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const result = await request('/usuarios', {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        if (result.ok) {
            showMessage('Conta criada com sucesso! Faça login para continuar.', false);
            setTimeout(() => {
                switchForm('login');
            }, 1500);
        } else {
            showMessage(result.data.mensagem || 'Erro ao criar conta', true);
        }
    } catch (error) {
        showMessage(error.message, true);
    }
});

editProfileBtn.addEventListener('click', () => {
    editProfileForm.style.display = 'flex';
});

cancelEdit.addEventListener('click', (e) => {
    e.preventDefault();
    editProfileForm.style.display = 'none';
});

editProfileForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = formToJson(event.currentTarget);
        const id = payload.id;
        const { id: _, ...body } = payload;

        const result = await request(`/usuarios/${encodeURIComponent(id)}`, {
            method: 'PATCH',
            body: JSON.stringify(body),
        });

        if (result.ok) {
            showMessage('Perfil atualizado com sucesso!', false);
            const userData = JSON.parse(localStorage.getItem(userStorageKey) || '{}');
            Object.assign(userData, body);
            localStorage.setItem(userStorageKey, JSON.stringify(userData));
            setTimeout(() => {
                showProfile(userData);
            }, 500);
        } else {
            showMessage(result.data.mensagem || 'Erro ao atualizar perfil', true);
        }
    } catch (error) {
        showMessage(error.message, true);
    }
});

logoutBtn.addEventListener('click', async () => {
    const token = localStorage.getItem(tokenStorageKey);

    if (!token) {
        // Já deslogado no cliente - apenas limpar estado local
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);
        showMessage('Logout realizado com sucesso!', false);
        setTimeout(() => {
            hideProfile();
            switchForm('login');
        }, 500);
        return;
    }

    try {
        // Server will read refresh token from HttpOnly cookie; no need to send it in the body.
        const result = await request('/usuarios/logout', { method: 'POST', body: '{}' });

        // Independentemente da resposta do servidor (token expirado/erro),
        // remover o token local para não deixar o cliente em estado inconsistente.
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);

        if (result && result.ok) {
            showMessage('Logout realizado com sucesso!', false);
        } else {
            // Se o servidor respondeu com 401/403/500, ainda consideramos o cliente deslogado.
            showMessage(result && result.data && result.data.mensagem ? result.data.mensagem : 'Logout concluído (token removido localmente).', false);
        }

        setTimeout(() => {
            hideProfile();
            switchForm('login');
        }, 500);
    } catch (error) {
        // Em caso de falha de rede, também limpar o estado local e informar o usuário.
        console.warn('Logout request failed:', error);
        localStorage.removeItem(tokenStorageKey);
        localStorage.removeItem(userStorageKey);
        showMessage('Logout concluído (sem conexão com o servidor).', false);
        setTimeout(() => {
            hideProfile();
            switchForm('login');
        }, 500);
    }
});

closeProfile.addEventListener('click', () => {
    hideProfile();
});

modalOverlay.addEventListener('click', () => {
    hideProfile();
});

// Initialize
saveBaseUrl(getBaseUrl());
checkLogin();
