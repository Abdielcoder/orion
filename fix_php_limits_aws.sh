#!/bin/bash

echo "🔧 Configurando límites de PHP en AWS EC2..."
echo "============================================="

# Archivo php.ini de Apache
PHP_INI="/etc/php/8.3/apache2/php.ini"

if [ ! -f "$PHP_INI" ]; then
    echo "❌ Error: No se encontró el archivo php.ini de Apache en $PHP_INI"
    exit 1
fi

echo "📁 Archivo php.ini encontrado: $PHP_INI"

# Crear backup del archivo original
echo "💾 Creando backup del archivo original..."
sudo cp "$PHP_INI" "$PHP_INI.backup.$(date +%Y%m%d_%H%M%S)"

# Configuraciones a modificar
echo "⚙️ Configurando límites de upload..."
sudo sed -i "s/^upload_max_filesize = .*/upload_max_filesize = 2G/" "$PHP_INI"
sudo sed -i "s/^post_max_size = .*/post_max_size = 2G/" "$PHP_INI"
sudo sed -i "s/^max_execution_time = .*/max_execution_time = 3600/" "$PHP_INI"
sudo sed -i "s/^max_input_time = .*/max_input_time = 3600/" "$PHP_INI"
sudo sed -i "s/^memory_limit = .*/memory_limit = 512M/" "$PHP_INI"

# Configuraciones adicionales para uploads largos
echo "📝 Agregando configuraciones adicionales..."

# Verificar si las configuraciones ya existen
if ! grep -q "^max_file_uploads" "$PHP_INI"; then
    echo "max_file_uploads = 20" | sudo tee -a "$PHP_INI"
fi

if ! grep -q "^default_socket_timeout" "$PHP_INI"; then
    echo "default_socket_timeout = 3600" | sudo tee -a "$PHP_INI"
fi

# Configurar sesión para uploads largos
sudo sed -i "s/^session.gc_maxlifetime = .*/session.gc_maxlifetime = 7200/" "$PHP_INI"
sudo sed -i "s/^session.cookie_lifetime = .*/session.cookie_lifetime = 7200/" "$PHP_INI"

echo ""
echo "✅ Configuraciones aplicadas:"
echo "================================"
echo "upload_max_filesize = 2G"
echo "post_max_size = 2G"
echo "max_execution_time = 3600"
echo "max_input_time = 3600"
echo "memory_limit = 512M"
echo "max_file_uploads = 20"
echo "default_socket_timeout = 3600"
echo "session.gc_maxlifetime = 7200"
echo "session.cookie_lifetime = 7200"

echo ""
echo "🔄 Reiniciando Apache..."
sudo systemctl restart apache2

if [ $? -eq 0 ]; then
    echo "✅ Apache reiniciado correctamente"
else
    echo "❌ Error al reiniciar Apache"
    exit 1
fi

echo ""
echo "🧪 Verificando configuraciones..."
echo "================================="
echo "PHP Upload Max Filesize: $(php -r "echo ini_get('upload_max_filesize');")"
echo "PHP Post Max Size: $(php -r "echo ini_get('post_max_size');")"
echo "PHP Max Execution Time: $(php -r "echo ini_get('max_execution_time');")"
echo "PHP Memory Limit: $(php -r "echo ini_get('memory_limit');")"

echo ""
echo "🎉 ¡Configuración completada!"
echo "============================="
echo "Ahora puedes subir archivos de hasta 2GB"
echo "Tiempo máximo de ejecución: 1 hora"
echo "Memoria máxima: 512MB"
