<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidor EP-3</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; justify-content: center; padding: 40px 20px; }
        .container { max-width: 800px; width: 100%; }
        h1 { font-size: 1.5rem; margin-bottom: 4px; }
        .subtitle { color: #94a3b8; margin-bottom: 24px; font-size: 0.875rem; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-on { background: #166534; color: #bbf7d0; }
        .card { background: #1e293b; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .card h2 { font-size: 1.1rem; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        .card h2 span { font-size: 0.8rem; color: #94a3b8; font-weight: 400; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th { text-align: left; color: #94a3b8; font-weight: 500; padding: 8px 4px; border-bottom: 1px solid #334155; }
        td { padding: 10px 4px; border-bottom: 1px solid #1e293b; }
        tr:last-child td { border-bottom: none; }
        .vazio { color: #64748b; font-style: italic; padding: 12px 0; }
        .tag { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 500; }
        .tag-adm { background: #1e3a5f; color: #93c5fd; }
        .tag-comum { background: #1a2e1a; color: #86efac; }
        .links { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .links a { color: #38bdf8; text-decoration: none; font-size: 0.875rem; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Servidor EP-3</h1>
        <div class="subtitle">
            <span class="badge badge-on">Online</span>
            &middot; Projeto Cliente/Servidor
        </div>

        <div class="card">
            <h2>🟢 Usuários Logados <span>({{ $logados->count() }})</span></h2>
            @if ($logados->isEmpty())
                <div class="vazio">Nenhum usuário logado no momento.</div>
            @else
                <table>
                    <thead>
                        <tr><th>Usuário</th><th>Nome</th><th>IP</th><th>Login</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($logados as $u)
                            <tr>
                                <td><strong>{{ $u['usuario'] }}</strong></td>
                                <td>{{ $u['nome'] }}</td>
                                <td>{{ $u['ip'] }}</td>
                                <td>{{ $u['login'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card">
            <h2>👤 Usuários Disponíveis <span>({{ $disponiveis->count() }})</span></h2>
            <table>
                <thead>
                    <tr><th>Usuário</th><th>Nome</th><th>Tipo</th><th>Senha</th></tr>
                </thead>
                <tbody>
                    @foreach ($disponiveis as $u)
                        <tr>
                            <td><strong>{{ $u['usuario'] }}</strong></td>
                            <td>{{ $u['nome'] }}</td>
                            <td><span class="tag tag-{{ $u['tipo'] }}">{{ $u['tipo'] }}</span></td>
                            <td><code>{{ $u['senha'] }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="links">
            <a href="/up">🔍 Health Check</a>
            <a href="/home">📱 Feed</a>
            <a href="{{ url('/cliente/index.php') }}">🧪 Painel de Testes</a>
        </div>
    </div>
</body>
</html>
