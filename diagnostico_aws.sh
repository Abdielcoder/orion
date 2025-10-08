#!/bin/bash

echo "🔍 DIAGNÓSTICO AWS EC2 - Error 500"
echo "=================================="

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}1. Verificando mod_rewrite...${NC}"
if apache2ctl -M | grep -q rewrite; then
    echo -e "${GREEN}✅ mod_rewrite está habilitado${NC}"
else
    echo -e "${RED}❌ mod_rewrite NO está habilitado${NC}"
    echo "Ejecutando: sudo a2enmod rewrite"
    sudo a2enmod rewrite
    sudo systemctl restart apache2
    echo -e "${GREEN}✅ mod_rewrite habilitado${NC}"
fi

echo ""
echo -e "${YELLOW}2. Verificando permisos de archivos...${NC}"
WEB_ROOT="/var/www/html/biblioteca"
if [ -d "$WEB_ROOT" ]; then
    echo "Ajustando permisos para $WEB_ROOT..."
    sudo chown -R www-data:www-data "$WEB_ROOT"
    sudo find "$WEB_ROOT" -type d -exec chmod 755 {} \;
    sudo find "$WEB_ROOT" -type f -exec chmod 644 {} \;
    sudo chmod -R 775 "$WEB_ROOT/storage/files" 2>/dev/null || echo "Directorio storage/files no existe"
    echo -e "${GREEN}✅ Permisos ajustados${NC}"
else
    echo -e "${RED}❌ Directorio $WEB_ROOT no existe${NC}"
fi

echo ""
echo -e "${YELLOW}3. Verificando archivos .htaccess...${NC}"

# Verificar .htaccess en la raíz del proyecto
HTACCESS_ROOT="$WEB_ROOT/.htaccess"
if [ -f "$HTACCESS_ROOT" ]; then
    echo "✅ .htaccess encontrado en la raíz del proyecto"
    echo "Contenido actual:"
    cat "$HTACCESS_ROOT"
else
    echo -e "${RED}❌ .htaccess NO encontrado en la raíz del proyecto${NC}"
    echo "Creando .htaccess simplificado..."
    cat <<EOF | sudo tee "$HTACCESS_ROOT" > /dev/null
RewriteEngine On
RewriteRule ^$ public/index.php/auth/login [L]
EOF
    echo -e "${GREEN}✅ .htaccess creado${NC}"
fi

# Verificar .htaccess en public
HTACCESS_PUBLIC="$WEB_ROOT/public/.htaccess"
if [ -f "$HTACCESS_PUBLIC" ]; then
    echo "✅ .htaccess encontrado en public/"
    echo "Contenido actual:"
    cat "$HTACCESS_PUBLIC"
else
    echo -e "${RED}❌ .htaccess NO encontrado en public/${NC}"
    echo "Creando .htaccess simplificado para public..."
    cat <<EOF | sudo tee "$HTACCESS_PUBLIC" > /dev/null
RewriteEngine On
RewriteBase /biblioteca/public/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
EOF
    echo -e "${GREEN}✅ .htaccess creado en public/${NC}"
fi

echo ""
echo -e "${YELLOW}4. Verificando configuración de Apache...${NC}"

# Verificar Virtual Host
VHOST_FILE="/etc/apache2/sites-available/biblioteca.conf"
if [ -f "$VHOST_FILE" ]; then
    echo "✅ Virtual Host encontrado"
    if grep -q "AllowOverride All" "$VHOST_FILE"; then
        echo -e "${GREEN}✅ AllowOverride All configurado${NC}"
    else
        echo -e "${RED}❌ AllowOverride All NO configurado${NC}"
        echo "Agregando AllowOverride All..."
        sudo sed -i '/<Directory \/var\/www\/html\/biblioteca\/public>/a\\t\tAllowOverride All' "$VHOST_FILE"
        echo -e "${GREEN}✅ AllowOverride All agregado${NC}"
    fi
else
    echo -e "${RED}❌ Virtual Host NO encontrado${NC}"
    echo "Creando Virtual Host..."
    cat <<EOF | sudo tee "$VHOST_FILE" > /dev/null
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html/biblioteca/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF
    sudo a2ensite biblioteca.conf
    echo -e "${GREEN}✅ Virtual Host creado y habilitado${NC}"
fi

echo ""
echo -e "${YELLOW}5. Reiniciando Apache...${NC}"
sudo systemctl restart apache2
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Apache reiniciado correctamente${NC}"
else
    echo -e "${RED}❌ Error al reiniciar Apache${NC}"
fi

echo ""
echo -e "${YELLOW}6. Verificando logs de error recientes...${NC}"
echo "Últimos 10 errores de Apache:"
sudo tail -10 /var/log/apache2/error.log

echo ""
echo -e "${GREEN}🎉 DIAGNÓSTICO COMPLETADO${NC}"
echo "=================================="
echo ""
echo "🔗 Prueba tu aplicación en:"
echo "http://98.87.243.120/biblioteca/public/index.php/auth/login"
echo ""
echo "Si aún tienes problemas, revisa los logs:"
echo "sudo tail -f /var/log/apache2/error.log"
