param(
    [string]$OutDir = "logs"
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path $OutDir)) {
    New-Item -ItemType Directory -Path $OutDir | Out-Null
}

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$serverLogSrc = "storage\logs\laravel.log"
$serverLogDst = Join-Path $OutDir ("servidor-" + $timestamp + ".log")
$readmeDst = Join-Path $OutDir ("como-exportar-log-cliente-" + $timestamp + ".txt")

if (Test-Path $serverLogSrc) {
    Copy-Item $serverLogSrc $serverLogDst -Force
    Write-Host "[OK] Log do servidor copiado para: $serverLogDst"
} else {
    Write-Host "[AVISO] Log do servidor não encontrado em $serverLogSrc"
}

@"
Como exportar o log do cliente (localStorage) no navegador:

1) Abra http://127.0.0.1:8001/testes
2) Pressione F12 e vá em Console
3) Execute:
   copy(localStorage.getItem('ep1_client_logs'))
4) Cole o conteúdo em um arquivo chamado cliente-$timestamp.json dentro de $OutDir

Opcional (resposta da listagem de usuários):
- Copie também o conteúdo exibido no bloco "Usuários retornados pelo servidor".
"@ | Set-Content -Path $readmeDst -Encoding UTF8

Write-Host "[OK] Instruções do log do cliente salvas em: $readmeDst"
Write-Host "[OK] Coleta finalizada."
