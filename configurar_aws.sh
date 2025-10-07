#!/bin/bash

# ============================================
# Script de configuración para AWS EC2
# Ejecutar en el servidor después de subir archivos
# ============================================

echo "🚀 Configurando Biblioteca Digital en AWS EC2..."
echo "================================================"

# Variables
WEB_DIR="/var/www/html"
APP_DIR="$WEB_DIR/biblioteca"
APACHE_USER="www-data"

# 1. Mover archivos al directorio web
echo "📁 Moviendo archivos al directorio web..."
if [ -d "/home/ubuntu/biblioteca" ]; then
    sudo mv /home/ubuntu/biblioteca $WEB_DIR/
    echo "✅ Archivos movidos a $APP_DIR"
else
    echo "❌ No se encontró /home/ubuntu/biblioteca"
    exit 1
fi

# 2. Configurar permisos
echo "🔐 Configurando permisos..."
sudo chown -R $APACHE_USER:$APACHE_USER $APP_DIR
sudo chmod -R 755 $APP_DIR
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/storage/files
echo "✅ Permisos configurados"

# 3. Crear directorio storage si no existe
echo "📂 Verificando directorio storage..."
sudo mkdir -p $APP_DIR/storage/files
sudo chown -R $APACHE_USER:$APACHE_USER $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/storage
echo "✅ Directorio storage listo"

# 4. Configurar .htaccess en raíz del servidor
echo "⚙️ Configurando .htaccess principal..."
sudo tee $WEB_DIR/.htaccess > /dev/null << 'EOF'
RewriteEngine On

# Redirección principal
RewriteCond %{REQUEST_URI} ^/$
RewriteRule ^(.*)$ /biblioteca/public/index.php/auth/login [R=301,L]

RewriteCond %{REQUEST_URI} ^/biblioteca/?$
RewriteRule ^(.*)$ /biblioteca/public/index.php/auth/login [R=301,L]

RewriteCond %{REQUEST_URI} ^/biblioteca/public/?$
RewriteRule ^(.*)$ /biblioteca/public/index.php/auth/login [R=301,L]

# Seguridad
ServerSignature Off
Options -Indexes

<FilesMatch "\.(sql|md|sh|log)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
EOF
echo "✅ .htaccess principal configurado"

# 5. Habilitar mod_rewrite
echo "🔄 Habilitando mod_rewrite..."
sudo a2enmod rewrite
sudo a2enmod headers
echo "✅ Módulos habilitados"

# 6. Configurar Virtual Host
echo "🌐 Configurando Virtual Host..."
sudo tee /etc/apache2/sites-available/biblioteca.conf > /dev/null << EOF
<VirtualHost *:80>
    ServerName 98.87.243.120
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    
    <Directory /var/www/html/biblioteca>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/biblioteca_error.log
    CustomLog \${APACHE_LOG_DIR}/biblioteca_access.log combined
</VirtualHost>
EOF

# Habilitar el sitio
sudo a2ensite biblioteca.conf
sudo a2dissite 000-default.conf
echo "✅ Virtual Host configurado"

# 7. Configurar PHP para archivos grandes
echo "📝 Configurando PHP..."
PHP_INI="/etc/php/8.1/apache2/php.ini"
if [ -f "$PHP_INI" ]; then
    sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 256M/' $PHP_INI
    sudo sed -i 's/post_max_size = .*/post_max_size = 256M/' $PHP_INI
    sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' $PHP_INI
    sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' $PHP_INI
    echo "✅ PHP configurado"
else
    echo "⚠️ No se encontró php.ini, configúralo manualmente"
fi

# 8. Reiniciar Apache
echo "🔄 Reiniciando Apache..."
sudo systemctl restart apache2
sudo systemctl enable apache2
echo "✅ Apache reiniciado"

# 9. Verificar estado
echo "🔍 Verificando configuración..."
echo "Estado de Apache:"
sudo systemctl status apache2 --no-pager -l

echo ""
echo "================================================"
echo "🎉 ¡Configuración completada!"
echo "================================================"
echo ""
echo "📋 URLs disponibles:"
echo "🌐 Aplicación: http://98.87.243.120"
echo "🌐 Login directo: http://98.87.243.120/biblioteca/public/index.php/auth/login"
echo ""
echo "👤 Credenciales por defecto:"
echo "Email: admin@biblioteca.com"
echo "Password: admin123"
echo ""
echo "⚠️ IMPORTANTE:"
echo "1. Configura la base de datos en config/database.php"
echo "2. Instala MySQL y ejecuta install_remote.sql"
echo "3. Cambia el password del administrador"
echo ""
echo "📊 Para verificar logs:"
echo "sudo tail -f /var/log/apache2/biblioteca_error.log"
