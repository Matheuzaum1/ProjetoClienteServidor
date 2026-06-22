#!/usr/bin/env bash
# run.sh — Gerenciador unificado do projeto (Linux/Mac)
# Uso: ./run.sh [comando]
# Comandos: install, setup, server, client, logs, help

set -euo pipefail
ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT_DIR"

show_help() {
    cat <<EOF
Uso: ./run.sh [comando]

Comandos:
  install    Instalar dependencias do Composer
  setup      Criar banco, rodar migrations e seed
  server     Iniciar servidor Laravel (nova janela)
  client     Iniciar servidor PHP do cliente (nova janela)
  logs       Coletar logs do servidor
  help       Mostrar esta ajuda

Exemplos:
  ./run.sh install
  ./run.sh setup
  ./run.sh server
  ./run.sh client
  ./run.sh logs
EOF
}

install_deps() {
    echo "=== Instalando dependencias do Composer ==="
    if ! command -v composer &>/dev/null; then
        echo "[ERRO] Composer nao encontrado. Instale https://getcomposer.org" >&2
        exit 1
    fi
    composer install --no-interaction --prefer-dist
    echo "[OK] Dependencias instaladas"
}

setup_db() {
    echo "=== Configurando banco de dados ==="
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            echo "[OK] .env criado a partir de .env.example"
            echo "[AVISO] Configure o banco em .env antes de continuar"
            php artisan key:generate
        else
            echo "[ERRO] .env.example nao encontrado" >&2
            exit 1
        fi
    fi

    read -r -p "Deseja recriar o banco (migrate:fresh --seed)? (s/N) " confirm
    if [ "$confirm" = "s" ] || [ "$confirm" = "S" ]; then
        php artisan migrate:fresh --seed --force
        echo "[OK] Banco recriado e seed aplicado"
    else
        php artisan migrate --force
        php artisan db:seed --force
        echo "[OK] Migrations e seed aplicados"
    fi
}

start_server() {
    read -r -p "Digite a porta para o servidor Laravel (Enter = 25000): " port
    port=${port:-25000}
    echo "=== Iniciando servidor na porta $port ==="

    local cmd="php artisan serve --host=0.0.0.0 --port=$port"

    if command -v gnome-terminal &>/dev/null; then
        gnome-terminal -- bash -c "$cmd; exec bash" &
    elif command -v xterm &>/dev/null; then
        xterm -e "$cmd; exec bash" &
    elif command -v x-terminal-emulator &>/dev/null; then
        x-terminal-emulator -e "$cmd" &
    elif command -v konsole &>/dev/null; then
        konsole --new-tab -e "$cmd" &
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        osascript -e "tell application \"Terminal\" to do script \"cd $ROOT_DIR && $cmd\""
    else
        echo "[AVISO] Nao foi possivel abrir nova janela. Iniciando aqui..."
        $cmd
    fi
    echo "[OK] Servidor iniciando em http://0.0.0.0:$port"
}

start_client() {
    read -r -p "Digite a porta para o cliente PHP (Enter = 8001): " port
    port=${port:-8001}
    echo "=== Iniciando cliente em http://127.0.0.1:$port ==="

    local cmd="php -S 127.0.0.1:$port -t cliente"

    if command -v gnome-terminal &>/dev/null; then
        gnome-terminal -- bash -c "$cmd; exec bash" &
    elif command -v xterm &>/dev/null; then
        xterm -e "$cmd; exec bash" &
    elif command -v x-terminal-emulator &>/dev/null; then
        x-terminal-emulator -e "$cmd" &
    elif command -v konsole &>/dev/null; then
        konsole --new-tab -e "$cmd" &
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        osascript -e "tell application \"Terminal\" to do script \"cd $ROOT_DIR && $cmd\""
    else
        echo "[AVISO] Nao foi possivel abrir nova janela. Iniciando aqui..."
        $cmd
    fi
    echo "[OK] Cliente disponivel em http://127.0.0.1:$port"
    echo "[OK] Testes em http://127.0.0.1:$port/testes"
}

collect_logs() {
    local outDir="logs"
    mkdir -p "$outDir"
    local timestamp
    timestamp=$(date +%Y%m%d-%H%M%S)
    local serverLogSrc="storage/logs/laravel.log"
    local serverLogDst="$outDir/servidor-$timestamp.log"
    local readmeDst="$outDir/como-exportar-log-cliente-$timestamp.txt"

    if [ -f "$serverLogSrc" ]; then
        cp "$serverLogSrc" "$serverLogDst"
        echo "[OK] Log do servidor: $serverLogDst"
    else
        echo "[AVISO] Log do servidor nao encontrado em $serverLogSrc"
    fi

    cat > "$readmeDst" <<- EOM
Como exportar o log do cliente (localStorage):
1) Abra http://127.0.0.1:8001/testes
2) Pressione F12 > Console
3) Execute: copy(localStorage.getItem('ep1_client_logs'))
4) Cole o conteudo em cliente-$timestamp.json dentro de $outDir
EOM

    echo "[OK] Instrucoes salvas em: $readmeDst"
    echo "[OK] Coleta finalizada"
}

# Main dispatch
case "${1:-}" in
    install) install_deps ;;
    setup)   setup_db ;;
    server)  start_server ;;
    client)  start_client ;;
    logs)    collect_logs ;;
    help|--help|-h) show_help ;;
    *)
        if [ -z "${1:-}" ]; then
            show_help
            echo ""
            echo "--- Executando padrao: install + setup ---"
            install_deps
            setup_db
            echo ""
            echo "Agora execute:"
            echo "  ./run.sh server   (iniciar servidor)"
            echo "  ./run.sh client   (iniciar cliente)"
        else
            echo "[ERRO] Comando desconhecido: $1" >&2
            show_help
            exit 1
        fi
        ;;
esac
