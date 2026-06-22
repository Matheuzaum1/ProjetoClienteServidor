#!/usr/bin/env pwsh
# run.ps1 — Gerenciador unificado do projeto (Windows/PowerShell)
# Uso: .\run.ps1 [comando]
# Comandos: install, setup, server, client, logs, help

param([string]$Command = "")

$rootDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location -LiteralPath $rootDir

function Show-Help {
    @"
Uso: .\run.ps1 [comando]

Comandos:
  install    Instalar dependencias do Composer
  setup      Criar banco, rodar migrations e seed
  server     Iniciar servidor Laravel (nova janela)
  client     Iniciar servidor PHP do cliente (nova janela)
  logs       Coletar logs do servidor
  help       Mostrar esta ajuda

Exemplos:
  .\run.ps1 install
  .\run.ps1 setup
  .\run.ps1 server
  .\run.ps1 client
  .\run.ps1 logs
"@
}

function Exec-Native {
    param([ScriptBlock]$Block)
    & $Block 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "Comando falhou com codigo $LASTEXITCODE"
    }
}

function Install-Deps {
    Write-Host "=== Instalando dependencias do Composer ===" -ForegroundColor Cyan
    if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
        Write-Host "[ERRO] Composer nao encontrado. Instale https://getcomposer.org" -ForegroundColor Red
        exit 1
    }
    Exec-Native { composer install --no-interaction --prefer-dist }
    Write-Host "[OK] Dependencias instaladas" -ForegroundColor Green
}

function Setup-Database {
    Write-Host "=== Configurando banco de dados ===" -ForegroundColor Cyan
    if (-not (Test-Path ".env")) {
        if (Test-Path ".env.example") {
            Copy-Item ".env.example" ".env"
            Write-Host "[OK] .env criado a partir de .env.example" -ForegroundColor Green
            Write-Host "[AVISO] Verifique a configuracao do banco em .env" -ForegroundColor Yellow
            Exec-Native { php artisan key:generate }
        } else {
            Write-Host "[ERRO] .env.example nao encontrado" -ForegroundColor Red
            exit 1
        }
    }

    $confirm = Read-Host "Deseja recriar o banco (migrate:fresh --seed)? (s/N)"
    if ($confirm -eq "s" -or $confirm -eq "S") {
        Exec-Native { php artisan migrate:fresh --seed --force }
        Write-Host "[OK] Banco recriado e seed aplicado" -ForegroundColor Green
    } else {
        Exec-Native { php artisan migrate --force }
        Exec-Native { php artisan db:seed --force }
        Write-Host "[OK] Migrations e seed aplicados" -ForegroundColor Green
    }
}

function Start-Server {
    $port = Read-Host "Digite a porta para o servidor Laravel (Enter = 25000)"
    if ([string]::IsNullOrWhiteSpace($port)) { $port = "25000" }
    Write-Host "=== Iniciando servidor na porta $port ===" -ForegroundColor Cyan
    $cmd = "php artisan serve --host=0.0.0.0 --port=$port"
    try {
        $psi = [System.Diagnostics.ProcessStartInfo]@{
            FileName = "pwsh"
            Arguments = "-NoExit -Command $cmd"
            UseShellExecute = $true
            WindowStyle = "Normal"
        }
        [System.Diagnostics.Process]::Start($psi) | Out-Null
    } catch {
        Start-Process pwsh -ArgumentList "-NoExit", "-Command", $cmd
    }
    Write-Host "[OK] Servidor em http://0.0.0.0:$port" -ForegroundColor Green
}

function Start-Client {
    Write-Host "=== Configuracao do servidor de destino ===" -ForegroundColor Cyan
    $target = Read-Host "Conectar ao servidor LOCAL (L) ou REMOTO (R)? (Enter = Local)"
    if ($target -eq "r" -or $target -eq "R") {
        $serverIp = Read-Host "Digite o IP do servidor remoto (ex: 192.168.1.100)"
        if ([string]::IsNullOrWhiteSpace($serverIp)) { $serverIp = "192.168.1.100" }
        $serverPort = Read-Host "Digite a porta do servidor remoto (Enter = 25000)"
        if ([string]::IsNullOrWhiteSpace($serverPort)) { $serverPort = "25000" }
    } else {
        $serverIp = "127.0.0.1"
        $serverPort = "25000"
    }

    $port = Read-Host "Digite a porta para o cliente PHP (Enter = 8001)"
    if ([string]::IsNullOrWhiteSpace($port)) { $port = "8001" }

    $config = @{
        serverIp   = $serverIp
        serverPort = $serverPort
        baseUrl    = "http://${serverIp}:${serverPort}"
    } | ConvertTo-Json

    Set-Content -Path "cliente\server-config.json" -Value $config -Encoding UTF8
    Write-Host "[OK] Configuracao salva: servidor $serverIp`:$serverPort" -ForegroundColor Green
    Write-Host "=== Iniciando cliente em http://127.0.0.1:$port ===" -ForegroundColor Cyan

    $cmd = "php -S 127.0.0.1:$port -t cliente router.php"
    try {
        $psi = [System.Diagnostics.ProcessStartInfo]@{
            FileName = "pwsh"
            Arguments = "-NoExit -Command $cmd"
            UseShellExecute = $true
            WindowStyle = "Normal"
        }
        [System.Diagnostics.Process]::Start($psi) | Out-Null
    } catch {
        Start-Process pwsh -ArgumentList "-NoExit", "-Command", $cmd
    }
    Write-Host "[OK] Cliente em http://127.0.0.1:$port" -ForegroundColor Green
    Write-Host "[OK] Conectando ao servidor: http://$serverIp`:$serverPort" -ForegroundColor Green
}

function Collect-Logs {
    $outDir = "logs"
    if (-not (Test-Path $outDir)) { New-Item -ItemType Directory -Path $outDir | Out-Null }
    $timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
    $serverLogSrc = "storage\logs\laravel.log"
    $serverLogDst = Join-Path $outDir "servidor-$timestamp.log"
    $readmeDst = Join-Path $outDir "como-exportar-log-cliente-$timestamp.txt"

    if (Test-Path $serverLogSrc) {
        Copy-Item $serverLogSrc $serverLogDst -Force
        Write-Host "[OK] Log do servidor: $serverLogDst" -ForegroundColor Green
    } else {
        Write-Host "[AVISO] Log do servidor nao encontrado" -ForegroundColor Yellow
    }

    @"
Como exportar o log do cliente (localStorage):
1) Abra http://127.0.0.1:8001/testes
2) Pressione F12 > Console
3) Execute: copy(localStorage.getItem('ep1_client_logs'))
4) Cole o conteudo em cliente-$timestamp.json dentro de $outDir
"@ | Set-Content -Path $readmeDst -Encoding UTF8

    Write-Host "[OK] Instrucoes salvas em: $readmeDst" -ForegroundColor Green
    Write-Host "[OK] Coleta finalizada" -ForegroundColor Green
}

# Main dispatch
try {
    switch ($Command.ToLower()) {
        "install"   { Install-Deps }
        "setup"     { Setup-Database }
        "server"    { Start-Server }
        "client"    { Start-Client }
        "logs"      { Collect-Logs }
        "help"      { Show-Help }
        default {
            if ([string]::IsNullOrWhiteSpace($Command)) {
                Show-Help
                Write-Host "`n--- Executando padrao: install + setup ---" -ForegroundColor Yellow
                Install-Deps
                Setup-Database
                Write-Host "`nAgora execute:" -ForegroundColor Cyan
                Write-Host "  .\run.ps1 server   (iniciar servidor)" -ForegroundColor White
                Write-Host "  .\run.ps1 client   (iniciar cliente)" -ForegroundColor White
            } else {
                Write-Host "[ERRO] Comando desconhecido: $Command" -ForegroundColor Red
                Show-Help
                exit 1
            }
        }
    }
} catch {
    Write-Host "[ERRO] $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Pressione qualquer tecla para sair..." -ForegroundColor Yellow
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    exit 1
}
