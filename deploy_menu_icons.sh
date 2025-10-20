#!/bin/bash

# Script para desplegar la corrección de iconos del menú contextual
# Uso: ./deploy_menu_icons.sh

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

echo -e "${GREEN}=== Desplegando corrección de iconos del menú contextual ===${NC}"
echo ""

# Verificar que existe el archivo PEM
if [ ! -f "$PEM_FILE" ]; then
    echo -e "${RED}Error: No se encuentra el archivo $PEM_FILE${NC}"
    echo "Por favor, asegúrate de que el archivo orion.pem esté en el directorio actual"
    exit 1
fi

# Establecer permisos correctos para el archivo PEM
chmod 400 "$PEM_FILE"

echo -e "${YELLOW}1. Copiando dashboard.php con iconos corregidos...${NC}"
scp -i "$PEM_FILE" \
    "$LOCAL_PATH/app/Views/drive/dashboard.php" \
    "$REMOTE_USER@$REMOTE_HOST:/tmp/dashboard.php"

echo -e "${YELLOW}2. Moviendo archivo a ubicación final...${NC}"

ssh -i "$PEM_FILE" "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
# Mover archivo desde /tmp a la ubicación final
sudo mv /tmp/dashboard.php /var/www/html/biblioteca/app/Views/drive/dashboard.php

# Establecer permisos correctos
sudo chown -R www-data:www-data /var/www/html/biblioteca/app/Views/drive/dashboard.php
sudo chmod 644 /var/www/html/biblioteca/app/Views/drive/dashboard.php

echo "✓ Dashboard.php actualizado y permisos establecidos correctamente"

# Verificar que el archivo existe
if [ -f "/var/www/html/biblioteca/app/Views/drive/dashboard.php" ]; then
    echo "✓ Dashboard.php instalado correctamente"
else
    echo "✗ Error: Dashboard.php no encontrado"
fi
ENDSSH

echo ""
echo -e "${GREEN}✓ Despliegue completado${NC}"
echo ""
echo -e "${GREEN}=== Corrección de iconos del menú contextual desplegada ===${NC}"
echo ""
echo -e "${YELLOW}Los iconos del menú contextual ahora deberían aparecer en blanco${NC}"
echo -e "${YELLOW}Para probar:${NC}"
echo "1. Ve a tu aplicación web"
echo "2. Haz clic derecho en cualquier archivo o carpeta"
echo "3. Los iconos del menú (Abrir, Renombrar, Compartir, Eliminar) deberían ser blancos"
echo ""

