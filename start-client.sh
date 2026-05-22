#!/bin/bash
read -p "Digite a porta para servir o cliente (ex: 8001): " PORTA
PORTA=${PORTA:-8001}
echo "Iniciando cliente em http://127.0.0.1:$PORTA ..."
php -S 127.0.0.1:$PORTA -t cliente
