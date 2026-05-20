<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cliente EP-2</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            width: 100%;
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 30px; text-align: center; }
        h2 { color: #667eea; margin-top: 25px; margin-bottom: 15px; font-size: 1.3em; }
        h3 { color: #764ba2; font-size: 1em; margin-top: 15px; }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }
        .form-group label {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover { background: #5568d3; }
        .toggle-login, .toggle-register {
            flex: 1;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            background: #cbd5e0;
            color: #2d3748;
        }
        .toggle-login.active, .toggle-register.active {
            background: #667eea;
            color: white;
        }
        .toggle-login:hover, .toggle-register:hover { background: #a0aec0; }
        .toggle-login.active:hover, .toggle-register.active:hover { background: #5568d3; }
        .btn-test {
            background: #48bb78;
            color: white;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn-test:hover { background: #38a169; }
        .btn-logout {
            background: #f56565;
            color: white;
            margin-top: 20px;
        }
        .btn-logout:hover { background: #e53e3e; }
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        .message.success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
            display: block;
        }
        .message.error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8787;
            display: block;
        }
        .hidden { display: none; }
        .section { margin-bottom: 30px; }
        .profile-info {
            background: #f7fafc;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .profile-info p { margin-bottom: 8px; }
        .profile-info strong { color: #667eea; }
        .output {
            background: #2d3748;
            color: #48bb78;
            padding: 15px;
            border-radius: 6px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Connection Form -->
    <div id="connectionScreen" class="section">
        <h1>🔗 Conectar ao Servidor</h1>
        <div id="connectionMessage" class="message"></div>
        <div class="form-group">
            <label>
                IP do Servidor:
                <input type="text" id="serverIp" placeholder="127.0.0.1 ou outro IP" value="127.0.0.1">
            </label>
            <label>
                Porta:
                <input type="number" id="serverPort" placeholder="25000" value="25000">
            </label>
            <button class="btn-primary" onclick="connectToServer()">Conectar</button>
        </div>
    </div>

    <!-- Login/Register Screen -->
    <div id="loginScreen" class="section hidden">
        <h1>🔐 Autenticação</h1>
        
        <!-- Toggle Buttons -->
        <div class="form-group" style="justify-content: center; margin-bottom: 25px;">
            <button class="btn-primary toggle-login active" onclick="toggleAuthMode('login')" style="border-radius: 6px 0 0 6px;">Entrar</button>
            <button class="btn-primary toggle-register" onclick="toggleAuthMode('register')" style="background: #cbd5e0; color: #2d3748; border-radius: 0 6px 6px 0;">Cadastrar</button>
        </div>

        <!-- Login Form -->
        <div id="loginForm">
            <div id="loginMessage" class="message"></div>
            <div class="form-group">
                <label>
                    Usuário:
                    <input type="text" id="loginUser" placeholder="admin ou seu usuário" value="admin">
                </label>
            </div>
            <div class="form-group">
                <label>
                    Senha:
                    <input type="password" id="loginPassword" placeholder="sua senha" value="admin123">
                </label>
            </div>
            <button class="btn-primary" onclick="performLogin()">Entrar</button>
        </div>

        <!-- Register Form -->
        <div id="registerForm" style="display: none;">
            <div id="registerMessage" class="message"></div>
            <div class="form-group">
                <label>
                    Nome:
                    <input type="text" id="registerName" placeholder="Seu nome completo">
                </label>
            </div>
            <div class="form-group">
                <label>
                    Usuário:
                    <input type="text" id="registerUser" placeholder="Nome de usuário único">
                </label>
            </div>
            <div class="form-group">
                <label>
                    Email:
                    <input type="email" id="registerEmail" placeholder="seu@email.com">
                </label>
            </div>
            <div class="form-group">
                <label>
                    Senha:
                    <input type="password" id="registerPassword" placeholder="Mínimo 6 caracteres">
                </label>
            </div>
            <div class="form-group">
                <label>
                    Confirmar Senha:
                    <input type="password" id="registerPasswordConfirm" placeholder="Confirme a senha">
                </label>
            </div>
            <button class="btn-primary" onclick="performRegister()">Cadastrar</button>
        </div>

        <button class="btn-primary" style="background: #cbd5e0; color: #2d3748; margin-top: 15px; width: 100%;" onclick="backToConnection()">← Voltar</button>
    </div>

    <!-- Dashboard (ADM) -->
    <div id="dashboardAdm" class="section hidden">
        <h1>👨‍💼 Painel Administrativo</h1>
        <div class="profile-info">
            <p><strong>Usuário:</strong> <span id="admUsername">-</span></p>
            <p><strong>Email:</strong> <span id="admEmail">-</span></p>
            <p><strong>Tipo:</strong> Administrador</p>
        </div>

        <h2>📋 Testes Avaliados (Admin)</h2>
        <div id="admMessage" class="message"></div>

        <h3>b) Ler cadastro de outros usuários (0,3)</h3>
        <button class="btn-test" onclick="testAdmRead()">Listar Usuários</button>
        <div id="outputRead" class="output" style="display: none;"></div>

        <h3>c) Atualizar cadastro de outro usuário (0,3)</h3>
        <button class="btn-test" onclick="testAdmUpdate()">Atualizar Usuário</button>
        <div id="outputUpdate" class="output" style="display: none;"></div>

        <h3>d) Apagar cadastro de outro usuário (0,3)</h3>
        <button class="btn-test" onclick="testAdmDelete()">Apagar Usuário</button>
        <div id="outputDelete" class="output" style="display: none;"></div>

        <h3>g) Mensagens de erro corretamente enviadas (0,2)</h3>
        <button class="btn-test" onclick="testErrorMessages()">Testar Erro (Dados Inválidos)</button>
        <div id="outputError" class="output" style="display: none;"></div>

        <button class="btn-logout" onclick="logout()">🚪 Sair</button>
    </div>

    <!-- Dashboard (Common User) -->
    <div id="dashboardCommon" class="section hidden">
        <h1>👤 Meu Perfil</h1>
        <div class="profile-info">
            <p><strong>Usuário:</strong> <span id="commonUsername">-</span></p>
            <p><strong>Email:</strong> <span id="commonEmail">-</span></p>
            <p><strong>Tipo:</strong> Usuário Comum</p>
        </div>

        <h2>📋 Testes Avaliados (Usuário Comum)</h2>
        <div id="commonMessage" class="message"></div>

        <h3>e) Não consegue editar dados de outro usuário (0,2)</h3>
        <button class="btn-test" onclick="testCommonEditFail()">Tentar Editar Outro</button>
        <div id="outputEditFail" class="output" style="display: none;"></div>

        <h3>f) Não consegue apagar dados de outro usuário (0,2)</h3>
        <button class="btn-test" onclick="testCommonDeleteFail()">Tentar Apagar Outro</button>
        <div id="outputDeleteFail" class="output" style="display: none;"></div>

        <h3>a) Mensagens de erro corretamente enviadas (0,2)</h3>
        <button class="btn-test" onclick="testErrorMessages()">Testar Erro (Dados Inválidos)</button>
        <div id="outputErrorCommon" class="output" style="display: none;"></div>

        <button class="btn-logout" onclick="logout()">🚪 Sair</button>
    </div>
</div>

<script src="assets/client.js"></script>
</body>
</html>
