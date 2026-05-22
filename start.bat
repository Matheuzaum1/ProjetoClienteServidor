@echo off
set /p PORTA="Digite a porta a ser usada no servidor Laravel (ex: 25000): "
if "%PORTA%"=="" set PORTA=25000
echo Iniciando servidor na porta %PORTA%...
php artisan serve --host=0.0.0.0 --port=%PORTA%
pause
