#!/bin/bash

# Script para desplegar el sistema JWT al servidor remoto
# Uso: ./deploy_jwt.sh

set -e  # Salir si hay algún error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuración
PEM_FILE="config/keys/orion.pem"
REMOTE_USER="ubuntu"
REMOTE_HOST="orion.rinorisk.com"
REMOTE_PATH="/var/www/html/biblioteca"
LOCAL_PATH="/Applications/MAMP/htdocs/biblioteca"

echo -e "${GREEN}=== Desplegando Sistema JWT ===${NC}"
echo ""

# Verificar que existe el archivo PEM
if [ ! -f "$PEM_FILE" ]; then
    echo -e "${RED}Error: No se encuentra el archivo $PEM_FILE${NC}"
    echo "Por favor, asegúrate de que el archivo orion.pem esté en config/keys/"
    exit 1
fi

# Establecer permisos correctos para el archivo PEM
chmod 400 "$PEM_FILE"

echo -e "${YELLOW}1. Haciendo git pull en el servidor remoto...${NC}"

ssh -i "$PEM_FILE" "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
cd /var/www/html/biblioteca
echo "Haciendo git pull..."
git pull origin master
echo "✓ Git pull completado"
ENDSSH

echo -e "${YELLOW}2. Copiando archivo orion.pem al servidor...${NC}"

scp -i "$PEM_FILE" \
    "$LOCAL_PATH/config/keys/orion.pem" \
    "$REMOTE_USER@$REMOTE_HOST:/tmp/orion.pem"

echo -e "${YELLOW}3. Configurando archivo orion.pem en el servidor...${NC}"

ssh -i "$PEM_FILE" "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
# Crear directorio si no existe
sudo mkdir -p /var/www/html/biblioteca/config/keys

# Mover archivo desde /tmp a la ubicación final
sudo mv /tmp/orion.pem /var/www/html/biblioteca/config/keys/orion.pem

# Establecer permisos correctos
sudo chown www-data:www-data /var/www/html/biblioteca/config/keys/orion.pem
sudo chmod 600 /var/www/html/biblioteca/config/keys/orion.pem

echo "✓ Archivo orion.pem configurado correctamente"

# Verificar que el archivo existe
if [ -f "/var/www/html/biblioteca/config/keys/orion.pem" ]; then
    echo "✓ orion.pem instalado correctamente"
else
    echo "✗ Error: orion.pem no encontrado"
fi
ENDSSH

echo -e "${YELLOW}4. Verificando archivos JWT en el servidor...${NC}"

ssh -i "$PEM_FILE" "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
echo "Verificando archivos del sistema JWT..."

# Verificar archivos principales
if [ -f "/var/www/html/biblioteca/app/Helpers/JwtHelper.php" ]; then
    echo "✓ JwtHelper.php encontrado"
else
    echo "✗ Error: JwtHelper.php no encontrado"
fi

if [ -f "/var/www/html/biblioteca/app/Middlewares/JwtAuthMiddleware.php" ]; then
    echo "✓ JwtAuthMiddleware.php encontrado"
else
    echo "✗ Error: JwtAuthMiddleware.php no encontrado"
fi

if [ -f "/var/www/html/biblioteca/test_jwt.php" ]; then
    echo "✓ test_jwt.php encontrado"
else
    echo "✗ Error: test_jwt.php no encontrado"
fi

# Verificar que no existe el login
if [ ! -f "/var/www/html/biblioteca/app/Views/auth/login.php" ]; then
    echo "✓ login.php eliminado correctamente"
else
    echo "⚠ Advertencia: login.php aún existe"
fi

echo ""
echo "Verificando configuración JWT..."
if grep -q "jwt" /var/www/html/biblioteca/config/config.php; then
    echo "✓ Configuración JWT encontrada en config.php"
else
    echo "✗ Error: Configuración JWT no encontrada"
fi
ENDSSH

echo ""
echo -e "${GREEN}=== Despliegue JWT completado ===${NC}"
echo ""
echo -e "${BLUE}Para probar el sistema JWT:${NC}"
echo "1. Accede a: http://orion.rinorisk.com/biblioteca/public/index.php/drive"
echo "2. El sistema debe autenticarte automáticamente con JWT"
echo "3. No debe aparecer formulario de login"
echo ""
echo -e "${BLUE}Para probar manualmente:${NC}"
echo "curl -H \"x-token: TU_TOKEN_JWT\" http://orion.rinorisk.com/biblioteca/public/index.php/drive/list"
echo ""
echo -e "${BLUE}Para ver logs:${NC}"
echo "ssh -i config/keys/orion.pem ubuntu@orion.rinorisk.com 'tail -f /var/log/apache2/error.log | grep JWT'"
echo ""
