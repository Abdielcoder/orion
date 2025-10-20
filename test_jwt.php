<?php
/**
 * Script de prueba para el sistema JWT
 * 
 * Uso desde línea de comandos:
 * php test_jwt.php
 * 
 * O desde navegador:
 * http://localhost:8888/biblioteca/test_jwt.php
 */

require __DIR__ . '/spl_autoload.php';

use App\Helpers\JwtHelper;

// Token de ejemplo del sistema Orion
$testToken = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiI2NjRlNTM2MTYyMzZmOGVkMTlkMjJiNWMiLCJhdG9tSWQiOiJVU1UyMDI0MDgxNjRaU0RQIiwibmFtZSI6IlN1cGVyIiwibmFtZVBhdGVybmFsIjoiQWRtaW5pc3RyYWRvciIsIm5hbWVNYXRlcm5hbCI6IkJFVEEiLCJzaG9ydE5hbWUiOiJTdXBlciBBZG1pbmlzdHJhZG9yIiwiZW1haWwiOiJkZXNhcnJvbGxvLWdlbmVyYWxAcmlub3Jpc2suY29tIiwiYXZhdGFyVXJsIjoiaHR0cHM6Ly9hcGlyaW5vLmNvbS9maWxlL3VzdWFyaW8vNjY0ZTUzNjE2MjM2ZjhlZDE5ZDIyYjVjLnBuZz8xNzU1NjIzMDc4MDQyIiwicm9sZSI6IlNVUEVSX0FETUlOX1JPTEUiLCJpYXQiOjE3NjA5OTgzNDcsImV4cCI6MTc2MTAxMjc0N30.u5gej0tpKwoq8H8henPt8rVUqXd9mG9B4079CfxcYz9x8bZD8s3Qd9ymBjdTntyMaO3WHQzbwLw5HfIcN6fSEvnHkT0bhSDKLdIJtX66HRjM8J1oCT9IcreMkGiay__F8SMAGG-pZ9WHdj69YNwsHWWMUMZLOlittuxpcvM7xdhK4q26gRFqtMrvxM65NXTilfNl2autzcviQiG7W1fwllhXS-3kyzRw_pKOq04Xrh1_HJpUziimbYFvDwDLtNSmWf2WBmIFMqO-8s_GwNez-32x26X6v1xlQ8nRZmFd4csS89jFwT8wghiZnkvmD8TmulUjH6uSnXxWlq4Ywjqk-g";

echo "=== PRUEBA DE DECODIFICACIÓN JWT ===\n\n";

echo "Token de prueba:\n";
echo substr($testToken, 0, 100) . "...\n\n";

// Decodificar token
echo "Decodificando token...\n";
$payload = JwtHelper::decode($testToken);

if ($payload) {
    echo "✓ Token decodificado correctamente\n\n";
    
    echo "Payload completo:\n";
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    // Extraer email
    $email = JwtHelper::getEmail($payload);
    echo "Email extraído: " . $email . "\n\n";
    
    // Extraer datos del usuario
    echo "Datos del usuario:\n";
    $userData = JwtHelper::getUserData($payload);
    echo json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    // Verificar expiración
    if (isset($payload['exp'])) {
        $exp = (int)$payload['exp'];
        $now = time();
        
        echo "Expiración:\n";
        echo "  - Timestamp de expiración: " . $exp . "\n";
        echo "  - Fecha de expiración: " . date('Y-m-d H:i:s', $exp) . "\n";
        echo "  - Timestamp actual: " . $now . "\n";
        echo "  - Fecha actual: " . date('Y-m-d H:i:s', $now) . "\n";
        
        if ($now > $exp) {
            echo "  ⚠ Token EXPIRADO\n";
        } else {
            $remaining = $exp - $now;
            echo "  ✓ Token VÁLIDO (expira en " . round($remaining / 60) . " minutos)\n";
        }
    }
    
} else {
    echo "✗ Error al decodificar token\n";
    echo "Verifica el formato del token y revisa los logs\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";

// Si se ejecuta desde el navegador, agregar formato HTML
if (php_sapi_name() !== 'cli') {
    echo "\n<style>body { font-family: monospace; white-space: pre; background: #1e1e1e; color: #d4d4d4; padding: 20px; }</style>";
}

