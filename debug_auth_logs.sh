#!/bin/bash

echo "=== DEBUG: Monitoreando logs de autenticación ==="
echo "Presiona Ctrl+C para salir"
echo ""

# Mostrar logs de error de PHP en tiempo real
ssh -i orion.pem ubuntu@orion.rinorisk.com "sudo tail -f /var/log/apache2/error.log | grep 'DEBUG Auth'"
