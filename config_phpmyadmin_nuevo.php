<?php
/**
 * Configuración limpia para phpMyAdmin
 * Copia este contenido al final de config.inc.php
 */

// Limpiar configuración anterior
unset($cfg['Servers']);

// Nueva configuración
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

// Configuración de seguridad
$cfg['blowfish_secret'] = 'biblioteca_digital_secret_key_2025_secure';
$cfg['VersionCheck'] = false;

// Configuración adicional
$cfg['DefaultLang'] = 'es';
$cfg['ServerDefault'] = 1;
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
?>
