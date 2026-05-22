<?php
$baseUrl = isset($_GET['baseUrl']) ? trim($_GET['baseUrl']) : 'http://127.0.0.1:25000';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instagram</title>
    <link rel="stylesheet" href="assets/styles-login.css">
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <!-- Left Side: Phone Mockup -->
        <div class="phone-mockup">
            <svg viewBox="0 0 320 640" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="igGradient" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#fd5949;stop-opacity:1" />
                        <stop offset="5%" style="stop-color:#d6249f;stop-opacity:1" />
                        <stop offset="45%" style="stop-color:#285AEB;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Screen -->
                <rect x="20" y="60" width="280" height="520" fill="#fafafa" rx="30"/>
                <!-- Status bar area -->
                <rect x="30" y="70" width="260" height="30" fill="#fafafa"/>
            </svg>
        </div>

        <!-- Right Side: Login/Register Form -->
        <div class="form-container">
            <div class="logo">
                <svg width="200" height="60" viewBox="0 0 200 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <text x="50" y="45" font-size="36" font-weight="bold" font-family="Arial" fill="#000">Instagram</text>
                </svg>
            </div>

            <!-- Server Config (Req. EP-2: IP and Port) -->
            <div class="server-config" style="display: flex; gap: 5px; margin-bottom: 20px;">
                <input id="serverIp" type="text" placeholder="IP (ex: 127.0.0.1)" value="127.0.0.1" style="flex: 2; padding: 8px; border: 1px solid #dbdbdb; border-radius: 4px;">
                <input id="serverPort" type="text" placeholder="Porta (ex: 25000)" value="25000" style="flex: 1; padding: 8px; border: 1px solid #dbdbdb; border-radius: 4px;">
                <button id="saveServerBtn" type="button" style="padding: 8px 12px; border: none; background: #0095f6; color: white; border-radius: 4px; font-weight: bold; cursor: pointer;">Salvar</button>
            </div>

            <!-- Toggle Buttons -->
            <div class="form-toggle">
                <button class="toggle-btn active" data-form="login">Login</button>
                <button class="toggle-btn" data-form="register">Criar Conta</button>
            </div>

            <!-- Login Form -->
            <form id="authLoginForm" class="auth-form active">
                <input type="text" name="usuario" placeholder="Usuário ou e-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" class="btn-submit">Entrar</button>
                <div class="divider">ou</div>
                <button type="button" class="btn-link" id="switchToRegister">Criar nova conta</button>
            </form>

            <!-- Register Form -->
            <form id="authRegisterForm" class="auth-form">
                <input type="text" name="nome" placeholder="Nome completo" required>
                <input type="text" name="usuario" placeholder="Nome de usuário" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                <input type="url" name="foto" placeholder="URL da foto (opcional)">
                <button type="submit" class="btn-submit">Cadastrar</button>
                <button type="button" class="btn-link" id="switchToLogin">Já tem uma conta? Faça login</button>
            </form>

            <!-- Response Message -->
            <div id="authMessage" class="auth-message" style="display: none;"></div>

            <!-- Demo User Info -->
            <div class="demo-info">
                <p><strong>Usuários de teste:</strong></p>
                <p>👨‍💼 Admin: admin / admin123</p>
                <p>👤 Comum: user1 / senha123</p>
            </div>

            <!-- Test Panel Button -->
            <div class="footer">
                <a href="testes/" class="btn-tests">🔧 Painel de Testes</a>
            </div>
        </div>
    </div>

    <!-- User Profile (shown after login) -->
    <div id="profileModal" class="profile-modal" style="display: none;">
        <div class="profile-content">
            <button class="btn-close" id="closeProfile">✕</button>
            
            <div class="profile-header">
                <img id="profilePhoto" src="https://via.placeholder.com/100" alt="Foto">
                <h2 id="profileName">Usuario</h2>
                <p id="profileUser">@usuario</p>
            </div>

            <div class="profile-info">
                <p><strong>E-mail:</strong> <span id="profileEmail">email@example.com</span></p>
                <p><strong>Biografia:</strong> <span id="profileBio">Sem biografia</span></p>
            </div>

            <div class="profile-actions">
                <button id="editProfileBtn" class="btn-edit">✏️ Editar Perfil</button>
                <button id="logoutBtn" class="btn-logout">🚪 Sair</button>
            </div>

            <!-- Edit Profile Form -->
            <form id="editProfileForm" class="edit-form" style="display: none;">
                <h3>Editar Perfil</h3>
                <input type="hidden" name="id" id="editUserId">
                <input type="text" name="nome" placeholder="Nome completo" required>
                <input type="text" name="usuario" placeholder="Nome de usuário" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <textarea name="biografia" placeholder="Biografia (opcional)" rows="2"></textarea>
                <input type="url" name="foto" placeholder="URL da foto (opcional)">
                <input type="password" name="senha" placeholder="Nova senha (opcional)">
                <button type="submit" class="btn-submit">Salvar Alterações</button>
                <button type="button" class="btn-cancel" id="cancelEdit">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Overlay for modals -->
    <div id="modalOverlay" class="modal-overlay" style="display: none;"></div>
</div>

<script src="assets/app-login.js"></script>
</body>
</html>
