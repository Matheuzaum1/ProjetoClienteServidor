// Global state
let baseUrl = '';
let currentUser = null;
let currentToken = null;

// Show/Hide screens
function showScreen(screenId) {
    document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
    document.getElementById(screenId).classList.remove('hidden');
}

function showMessage(elementId, text, isError = false) {
    const msgEl = document.getElementById(elementId);
    msgEl.textContent = text;
    msgEl.className = 'message ' + (isError ? 'error' : 'success');
}

// Connection
function connectToServer() {
    const ip = document.getElementById('serverIp').value.trim();
    const port = document.getElementById('serverPort').value.trim();

    if (!ip || !port) {
        showMessage('connectionMessage', 'IP e Porta são obrigatórios', true);
        return;
    }

    baseUrl = `http://${ip}:${port}`;
    showMessage('connectionMessage', `Conectado a ${baseUrl}`);
    setTimeout(() => showScreen('loginScreen'), 500);
}

function backToConnection() {
    baseUrl = '';
    currentUser = null;
    currentToken = null;
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerForm').style.display = 'none';
    document.querySelector('.toggle-login').classList.add('active');
    document.querySelector('.toggle-register').classList.remove('active');
    showScreen('connectionScreen');
}

// Toggle Auth Mode
function toggleAuthMode(mode) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const toggleLogin = document.querySelector('.toggle-login');
    const toggleRegister = document.querySelector('.toggle-register');

    if (mode === 'login') {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        toggleLogin.classList.add('active');
        toggleRegister.classList.remove('active');
    } else {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        toggleLogin.classList.remove('active');
        toggleRegister.classList.add('active');
    }
}

// Login
async function performLogin() {
    const user = document.getElementById('loginUser').value.trim();
    const pass = document.getElementById('loginPassword').value.trim();

    if (!user || !pass) {
        showMessage('loginMessage', 'Usuário e Senha são obrigatórios', true);
        return;
    }

    try {
        const res = await apiRequest('/usuarios/login', { method: 'POST', body: JSON.stringify({ usuario: user, senha: pass }) });
        
        if (!res.ok) {
            showMessage('loginMessage', res.data?.mensagem || 'Erro ao fazer login', true);
            return;
        }

        currentToken = res.data?.dados?.token;
        currentUser = res.data?.dados?.usuario;

        if (!currentToken || !currentUser) {
            showMessage('loginMessage', 'Token ou usuário não retornado', true);
            return;
        }

        // Show appropriate dashboard
        if (currentUser.tipo_usuario === 'adm') {
            document.getElementById('admUsername').textContent = currentUser.usuario || '-';
            document.getElementById('admEmail').textContent = currentUser.email || '-';
            showScreen('dashboardAdm');
        } else {
            document.getElementById('commonUsername').textContent = currentUser.usuario || '-';
            document.getElementById('commonEmail').textContent = currentUser.email || '-';
            showScreen('dashboardCommon');
        }
    } catch (err) {
        console.error('Erro ao fazer login:', err);
        showMessage('loginMessage', '❌ Erro de conexão: ' + err.message, true);
    }
}

// Register
async function performRegister() {
    const name = document.getElementById('registerName').value.trim();
    const user = document.getElementById('registerUser').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const pass = document.getElementById('registerPassword').value.trim();
    const passConfirm = document.getElementById('registerPasswordConfirm').value.trim();

    if (!name || !user || !email || !pass) {
        showMessage('registerMessage', 'Todos os campos são obrigatórios', true);
        return;
    }

    if (pass !== passConfirm) {
        showMessage('registerMessage', 'Senhas não conferem', true);
        return;
    }

    if (pass.length < 6) {
        showMessage('registerMessage', 'Senha deve ter mínimo 6 caracteres', true);
        return;
    }

    try {
        showMessage('registerMessage', '⏳ Cadastrando...', false);
        const res = await apiRequest('/usuarios', {
            method: 'POST',
            body: JSON.stringify({
                nome: name,
                usuario: user,
                email: email,
                senha: pass,
                tipo_usuario: 'comum'
            })
        });

        if (!res.ok) {
            showMessage('registerMessage', res.data?.mensagem || 'Erro ao cadastrar', true);
            return;
        }

        showMessage('registerMessage', '✅ Cadastro realizado com sucesso! Faça login agora.', false);
        setTimeout(() => {
            document.getElementById('loginUser').value = user;
            document.getElementById('loginPassword').value = pass;
            toggleAuthMode('login');
        }, 1500);
    } catch (err) {
        console.error('Erro ao registrar:', err);
        showMessage('registerMessage', '❌ Erro de conexão: ' + err.message, true);
    }
}

// API Request with token
async function apiRequest(path, options = {}) {
    const headers = new Headers(options.headers || {});
    headers.set('Content-Type', 'application/json');
    
    if (currentToken) {
        headers.set('Authorization', `Bearer ${currentToken}`);
    }

    console.log(`📡 [${options.method || 'GET'}] ${baseUrl}${path}`);

    try {
        const response = await fetch(baseUrl + path, {
            ...options,
            headers
        });

        const text = await response.text();
        let data = {};
        try {
            data = text ? JSON.parse(text) : {};
        } catch {
            data = { raw: text };
        }

        console.log(`📥 Status: ${response.status}`, data);
        return { status: response.status, ok: response.ok, data };
    } catch (error) {
        console.error('❌ Erro de fetch:', error);
        throw new Error(`Erro ao conectar ao servidor: ${error.message}`);
    }
}

function showOutput(elementId, data) {
    const el = document.getElementById(elementId);
    el.textContent = JSON.stringify(data, null, 2);
    el.style.display = 'block';
}

// ============= ADM Tests =============

async function testAdmRead() {
    try {
        showMessage('admMessage', '⏳ Listando usuários...', false);
        const res = await apiRequest('/usuarios', { method: 'GET' });
        
        if (res.ok) {
            // Extrair lista de usuários
            let usuarios = [];
            
            // Suportar diferentes formatos de resposta
            if (Array.isArray(res.data)) {
                usuarios = res.data;
            } else if (res.data?.dados) {
                usuarios = Array.isArray(res.data.dados) ? res.data.dados : [res.data.dados];
            } else if (res.data?.data) {
                usuarios = Array.isArray(res.data.data) ? res.data.data : [res.data.data];
            }

            // Formatar como tabela HTML
            let html = `<table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #667eea; color: white;">
                    <tr>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">ID</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Usuário</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Nome</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Email</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Tipo</th>
                    </tr>
                </thead>
                <tbody>`;

            usuarios.forEach(user => {
                const tipo = user.tipo_usuario === 'adm' || user.admin ? '👨‍💼 ADM' : '👤 Comum';
                html += `<tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">${user.id || '-'}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>${user.usuario || '-'}</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">${user.nome || '-'}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">${user.email || '-'}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${tipo}</td>
                </tr>`;
            });

            html += `</tbody></table>`;
            html += `<div style="margin-top: 15px; padding: 10px; background: #e6f3ff; border-radius: 6px;">
                <strong>Total:</strong> ${usuarios.length} usuário(s) encontrado(s)
            </div>`;

            const outputEl = document.getElementById('outputRead');
            outputEl.innerHTML = html;
            outputEl.style.display = 'block';
            outputEl.style.overflow = 'auto';

            showOutput('outputRead', { status: res.status, total: usuarios.length, usuarios: usuarios });
            showMessage('admMessage', `✅ Sucesso ao listar ${usuarios.length} usuário(s) (Item b)`, false);
        } else {
            showOutput('outputRead', { status: res.status, error: res.data });
            showMessage('admMessage', 'Erro: ' + (res.data?.mensagem || 'Falha ao listar'), true);
        }
    } catch (err) {
        showOutput('outputRead', { error: err.message });
        showMessage('admMessage', err.message, true);
    }
}

async function testAdmUpdate() {
    const userId = prompt('ID do usuário a atualizar:', '1');
    if (!userId) return;

    try {
        showMessage('admMessage', '⏳ Atualizando usuário...', false);
        
        // Primeiro, buscar dados do usuário para mostrar opções
        const getRes = await apiRequest(`/usuarios/${userId}`, { method: 'GET' });
        
        if (!getRes.ok) {
            showOutput('outputUpdate', { status: getRes.status, error: getRes.data });
            showMessage('admMessage', 'Erro: Usuário não encontrado', true);
            return;
        }

        const usuario = getRes.data?.dados?.usuario || getRes.data?.data || {};
        
        // Mostrar opções de campo a atualizar
        const campo = prompt(
            `Qual campo quer atualizar para o usuário ID ${userId}?\n\n` +
            `Dados atuais:\n` +
            `- nome: ${usuario.nome || '-'}\n` +
            `- email: ${usuario.email || '-'}\n` +
            `- usuario: ${usuario.usuario || '-'}\n` +
            `- biografia: ${usuario.biografia || '-'}\n\n` +
            `Digite: nome, email, usuario ou biografia`,
            'nome'
        );

        if (!campo || !['nome', 'email', 'usuario', 'biografia'].includes(campo.toLowerCase())) {
            showMessage('admMessage', '❌ Campo inválido', true);
            return;
        }

        const novoValor = prompt(`Novo valor para ${campo}:`);
        if (!novoValor) return;

        const updateData = {};
        updateData[campo.toLowerCase()] = novoValor;

        const res = await apiRequest(`/usuarios/${userId}`, {
            method: 'PATCH',
            body: JSON.stringify(updateData)
        });

        if (res.ok) {
            showOutput('outputUpdate', { status: res.status, data: res.data });
            showMessage('admMessage', `✅ Sucesso ao atualizar ${campo} (Item c)`, false);
        } else {
            showOutput('outputUpdate', { status: res.status, error: res.data });
            showMessage('admMessage', 'Erro: ' + (res.data?.mensagem || 'Falha ao atualizar'), true);
        }
    } catch (err) {
        showOutput('outputUpdate', { error: err.message });
        showMessage('admMessage', err.message, true);
    }
}

async function testAdmDelete() {
    const userId = prompt('ID do usuário a apagar:', '1');
    if (!userId) return;

    if (!confirm(`Tem certeza que quer apagar o usuário ${userId}?`)) return;

    try {
        showMessage('admMessage', '⏳ Apagando usuário...', false);
        const res = await apiRequest(`/usuarios/${userId}`, { method: 'DELETE' });

        if (res.ok) {
            showOutput('outputDelete', { status: res.status, data: res.data });
            showMessage('admMessage', '✅ Sucesso ao apagar usuário (Item d)', false);
        } else {
            showOutput('outputDelete', { status: res.status, error: res.data });
            showMessage('admMessage', 'Erro: ' + (res.data?.mensagem || 'Falha ao apagar'), true);
        }
    } catch (err) {
        showOutput('outputDelete', { error: err.message });
        showMessage('admMessage', err.message, true);
    }
}

// ============= Common User Tests =============

async function testCommonEditFail() {
    const userId = prompt('ID do usuário (tente editar outro):', '1');
    if (!userId) return;

    try {
        showMessage('commonMessage', '⏳ Tentando editar outro usuário...', false);
        const res = await apiRequest(`/usuarios/${userId}`, {
            method: 'PATCH',
            body: JSON.stringify({ nome: 'TentativaMaliciosa' })
        });

        if (!res.ok && (res.status === 403 || res.status === 401)) {
            showOutput('outputEditFail', { status: res.status, error: res.data });
            showMessage('commonMessage', '✅ Servidor bloqueou corretamente (Item e)', false);
        } else if (res.ok) {
            showOutput('outputEditFail', { status: res.status, unexpected: 'Usuário foi alterado!' });
            showMessage('commonMessage', '❌ Servidor permitiu edição indevida', true);
        } else {
            showOutput('outputEditFail', { status: res.status, error: res.data });
            showMessage('commonMessage', 'Resposta: ' + (res.data?.mensagem || 'Erro desconhecido'), true);
        }
    } catch (err) {
        showOutput('outputEditFail', { error: err.message });
        showMessage('commonMessage', err.message, true);
    }
}

async function testCommonDeleteFail() {
    const userId = prompt('ID do usuário (tente apagar outro):', '1');
    if (!userId) return;

    try {
        showMessage('commonMessage', '⏳ Tentando apagar outro usuário...', false);
        const res = await apiRequest(`/usuarios/${userId}`, { method: 'DELETE' });

        if (!res.ok && (res.status === 403 || res.status === 401)) {
            showOutput('outputDeleteFail', { status: res.status, error: res.data });
            showMessage('commonMessage', '✅ Servidor bloqueou corretamente (Item f)', false);
        } else if (res.ok) {
            showOutput('outputDeleteFail', { status: res.status, unexpected: 'Usuário foi deletado!' });
            showMessage('commonMessage', '❌ Servidor permitiu deleção indevida', true);
        } else {
            showOutput('outputDeleteFail', { status: res.status, error: res.data });
            showMessage('commonMessage', 'Resposta: ' + (res.data?.mensagem || 'Erro desconhecido'), true);
        }
    } catch (err) {
        showOutput('outputDeleteFail', { error: err.message });
        showMessage('commonMessage', err.message, true);
    }
}

// ============= Error Messages Test =============

async function testErrorMessages() {
    try {
        showMessage(currentUser?.tipo_usuario === 'adm' ? 'admMessage' : 'commonMessage', '⏳ Testando mensagens de erro...', false);
        
        // Test invalid data
        const res = await apiRequest('/usuarios', {
            method: 'POST',
            body: JSON.stringify({}) // Empty body should fail validation
        });

        const outputId = currentUser?.tipo_usuario === 'adm' ? 'outputError' : 'outputErrorCommon';
        showOutput(outputId, { status: res.status, error: res.data });
        
        if (!res.ok) {
            showMessage(currentUser?.tipo_usuario === 'adm' ? 'admMessage' : 'commonMessage', 
                '✅ Servidor retornou mensagem de erro (Item ' + (currentUser?.tipo_usuario === 'adm' ? 'g' : 'a') + ')', false);
        } else {
            showMessage(currentUser?.tipo_usuario === 'adm' ? 'admMessage' : 'commonMessage', '❌ Servidor aceitou dados inválidos', true);
        }
    } catch (err) {
        const outputId = currentUser?.tipo_usuario === 'adm' ? 'outputError' : 'outputErrorCommon';
        showOutput(outputId, { error: err.message });
        showMessage(currentUser?.tipo_usuario === 'adm' ? 'admMessage' : 'commonMessage', err.message, true);
    }
}

// Logout
async function logout() {
    // Tentar notificar o servidor do logout
    if (currentToken && baseUrl) {
        try {
            console.log('📡 Notificando servidor do logout...');
            await apiRequest('/usuarios/logout', { method: 'POST' });
            console.log('✅ Logout confirmado no servidor');
        } catch (err) {
            console.warn('⚠️ Servidor não respondeu ao logout (continuando mesmo assim):', err.message);
        }
    }

    // Limpar estado local
    baseUrl = '';
    currentUser = null;
    currentToken = null;
    console.log('🚪 Logout concluído');
    showScreen('connectionScreen');
}

// Initialize
window.addEventListener('load', () => {
    showScreen('connectionScreen');
});
