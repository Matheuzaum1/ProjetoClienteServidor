@echo off
set /p PORTA="Digite a porta para servir o cliente (ex: 8001): "
if "%PORTA%"=="" set PORTA=8001
necho Iniciando cliente em http://127.0.0.1:%PORTA% ...
php -S 127.0.0.1:%PORTA% -t cliente
pause
