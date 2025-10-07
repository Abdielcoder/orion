#!/bin/bash

echo "🔧 Configurando phpMyAdmin5 con tus credenciales..."

CONFIG_FILE="/Applications/MAMP/bin/phpMyAdmin5/config.inc.php"

# Crear nueva configuración
cat > "$CONFIG_FILE" << 'EOF'
<?php
/**
 * phpMyAdmin configuration for MAMP
 * Configuración personalizada para biblioteca digital
 */

// Configuración de seguridad
$cfg['blowfish_secret'] = 'biblioteca_digital_phpMyAdmin5_secret_key_2025';
$cfg['VersionCheck'] = false;
$cfg['SendErrorReports'] = 'never';

// Servidor MySQL
$i = 0;
$i++;

$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['port'] = '8889';
$cfg['Servers'][$i]['socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

// Configuración adicional
$cfg['DefaultLang'] = 'es';
$cfg['ServerDefault'] = 1;
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['MaxRows'] = 25;
$cfg['Order'] = 'ASC';

// Configuración de interfaz
$cfg['ThemeDefault'] = 'pmahomme';
$cfg['NavigationTreePointerEnable'] = true;
$cfg['BrowsePointerEnable'] = true;
$cfg['BrowseMarkerEnable'] = true;

// Configuración de exportación
$cfg['Export']['compression'] = 'none';
$cfg['Export']['format'] = 'sql';

// Configuración de importación
$cfg['Import']['allow_interrupt'] = true;

?>
EOF

echo "✅ phpMyAdmin5 configurado!"
echo ""
echo "Ahora accede a:"
echo "🌐 http://localhost:8888/phpMyAdmin5"
echo ""
echo "Credenciales MySQL:"
echo "👤 Usuario: abdiel"
echo "🔑 Password: S4m3sg33k"
echo ""
echo "Una vez dentro, selecciona la base de datos 'biblioteca_digital'"
