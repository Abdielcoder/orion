#!/bin/bash

echo "🔄 Reiniciando MAMP para aplicar cambios de phpMyAdmin..."

# Detener MAMP
echo "⏹️  Deteniendo MAMP..."
osascript -e 'tell application "MAMP" to quit'
sleep 3

# Iniciar MAMP
echo "▶️  Iniciando MAMP..."
open -a MAMP
sleep 5

# Iniciar servidores
echo "🚀 Iniciando servidores..."
osascript -e 'tell application "MAMP" to activate'
osascript -e 'tell application "System Events" to tell process "MAMP" to click button "Start Servers" of window 1'

echo "✅ MAMP reiniciado!"
echo ""
echo "Ahora puedes acceder a:"
echo "📊 phpMyAdmin: http://localhost:8888/phpMyAdmin"
echo "🌐 Tu aplicación: http://localhost:8888/biblioteca/public/index.php/auth/login"
echo ""
echo "Credenciales de la aplicación:"
echo "Email: admin@biblioteca.com"
echo "Password: admin123"
