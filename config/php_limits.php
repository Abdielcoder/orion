<?php
/**
 * Configuración de límites de PHP para archivos grandes
 * NOTA: Estos límites solo funcionan si PHP permite cambiarlos en tiempo de ejecución.
 * Para archivos grandes, es mejor configurar estos valores en php.ini de MAMP.
 */

// Solo intentar configurar si está permitido
if (function_exists('ini_set')) {
    // Tiempo de ejecución
    @ini_set('max_execution_time', '300');
    @ini_set('max_input_time', '300');
    
    // Memoria
    @ini_set('memory_limit', '512M');
    
    // Timeouts
    @ini_set('default_socket_timeout', '300');
    
    // NOTA: upload_max_filesize y post_max_size generalmente no se pueden cambiar
    // en tiempo de ejecución y deben configurarse en php.ini
}

// Función para obtener el tamaño máximo de upload real
function getMaxUploadSize() {
    $upload_max = ini_get('upload_max_filesize');
    $post_max = ini_get('post_max_size');
    
    $upload_bytes = convertToBytes($upload_max);
    $post_bytes = convertToBytes($post_max);
    
    return min($upload_bytes, $post_bytes);
}

// Convertir valores de configuración a bytes
function convertToBytes($value) {
    $value = trim($value);
    $last = strtolower($value[strlen($value)-1]);
    $value = (int)$value;
    
    switch($last) {
        case 'g':
            $value *= 1024;
        case 'm':
            $value *= 1024;
        case 'k':
            $value *= 1024;
    }
    
    return $value;
}

// Log solo si estamos en modo debug
if (defined('DEBUG') && DEBUG) {
    error_log('PHP Upload Limits (Current):');
    error_log('- upload_max_filesize: ' . ini_get('upload_max_filesize'));
    error_log('- post_max_size: ' . ini_get('post_max_size'));
    error_log('- max_execution_time: ' . ini_get('max_execution_time'));
    error_log('- memory_limit: ' . ini_get('memory_limit'));
    error_log('- Max upload real: ' . number_format(getMaxUploadSize() / 1048576, 2) . ' MB');
}
?>
