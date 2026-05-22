#!/bin/bash
read -p "Digite a porta a ser usada no servidor Laravel (ex: 25000): " PORTA
PORTA=${PORTA:-25000}
echo "Iniciando servidor na porta $PORTA..."
php artisan serve --host=0.0.0.0 --port=$PORTA
