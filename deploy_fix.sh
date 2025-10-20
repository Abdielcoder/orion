#!/bin/bash

# Script para desplegar la corrección de enlaces compartidos al servidor remoto
# Uso: ./deploy_fix.sh

set -e  # Salir si hay algún error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuración
PEM_FILE="orion.pem"
REMOTE_USER="ubuntu"
REMOTE_HOST="orion.rinorisk.com"
REMOTE_PATH="/var/www/html/biblioteca"
LOCAL_PATH="/Applications/MAMP/htdocs/biblioteca"

echo -e "${GREEN}=== Desplegando corrección de enlaces compartidos ===${NC}"
echo ""

# Verificar que existe el archivo PEM
if [ ! -f "$PEM_FILE" ]; then
    echo -e "${RED}Error: No se encuentra el archivo $PEM_FILE${NC}"
    echo "Por favor, asegúrate de que el archivo orion.pem esté en el directorio actual"
    exit 1
fi

# Establecer permisos correctos para el archivo PEM
chmod 400 "$PEM_FILE"

echo -e "${YELLOW}1. Copiando archivos a directorio temporal...${NC}"
scp -i "$PEM_FILE" \
    "$LOCAL_PATH/app/Repositories/ShareLinkRepository.php" \
    "$REMOTE_USER@$REMOTE_HOST:/tmp/ShareLinkRepository.php"

scp -i "$PEM_FILE" \
    "$LOCAL_PATH/app/Controllers/ShareController.php" \
    "$REMOTE_USER@$REMOTE_HOST:/tmp/ShareController.php"

scp -i "$PEM_FILE" \
    "$LOCAL_PATH/app/Views/share/view.php" \
    "$REMOTE_USER@$REMOTE_HOST:/tmp/view.php"

echo -e "${YELLOW}2. Moviendo archivos a ubicación final...${NC}"

ssh -i "$PEM_FILE" "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
# Mover archivos desde /tmp a la ubicación final
sudo mv /tmp/ShareLinkRepository.php /var/www/html/biblioteca/app/Repositories/ShareLinkRepository.php
sudo mv /tmp/ShareController.php /var/www/html/biblioteca/app/Controllers/ShareController.php
sudo mv /tmp/view.php /var/www/html/biblioteca/app/Views/share/view.php

# Establecer permisos correctos
sudo chown -R www-data:www-data /var/www/html/biblioteca/app/Repositories/ShareLinkRepository.php
sudo chown -R www-data:www-data /var/www/html/biblioteca/app/Controllers/ShareController.php
sudo chown -R www-data:www-data /var/www/html/biblioteca/app/Views/share/view.php

sudo chmod 644 /var/www/html/biblioteca/app/Repositories/ShareLinkRepository.php
sudo chmod 644 /var/www/html/biblioteca/app/Controllers/ShareController.php
sudo chmod 644 /var/www/html/biblioteca/app/Views/share/view.php

echo "✓ Archivos movidos y permisos establecidos correctamente"

# Verificar que los archivos existen
if [ -f "/var/www/html/biblioteca/app/Repositories/ShareLinkRepository.php" ]; then
    echo "✓ ShareLinkRepository.php instalado"
else
    echo "✗ Error: ShareLinkRepository.php no encontrado"
fi

if [ -f "/var/www/html/biblioteca/app/Controllers/ShareController.php" ]; then
    echo "✓ ShareController.php instalado"
else
    echo "✗ Error: ShareController.php no encontrado"
fi

if [ -f "/var/www/html/biblioteca/app/Views/share/view.php" ]; then
    echo "✓ view.php instalado"
else
    echo "✗ Error: view.php no encontrado"
fi
ENDSSH

echo ""
echo -e "${GREEN}✓ Despliegue completado${NC}"

echo ""
echo -e "${GREEN}=== Despliegue completado exitosamente ===${NC}"
echo ""
echo -e "${YELLOW}Para probar:${NC}"
echo "1. Ve a tu aplicación web"
echo "2. Selecciona un archivo"
echo "3. Haz clic en 'Compartir'"
echo "4. Crea un enlace público"
echo "5. Abre el enlace en una ventana privada/incógnito"
echo ""
echo -e "${YELLOW}El enlace que estabas probando ahora debería funcionar:${NC}"
echo "http://orion.rinorisk.com/biblioteca/public/index.php/s/0eddd739b9fe0decb2c021fa98ed9e2a5412499036c166589b3ff2dae0b9fe02"
echo ""

