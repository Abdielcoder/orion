<?php

namespace App\Middlewares;

use App\Services\Config;
use App\Services\Session;

class CsrfMiddleware
{
    public function handle(callable $next)
    {
        $tokenName = Config::get('security.csrf_token_name', '_csrf');
        
        // Generar token si no existe (tanto para GET como POST)
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $token = $_POST[$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_GET[$tokenName] ?? '';
            $sessionToken = Session::get('csrf_token');
            
            // Debug para iframes
            error_log("DEBUG CSRF - Token recibido: " . substr($token, 0, 10) . "...");
            error_log("DEBUG CSRF - Token sesión: " . substr($sessionToken, 0, 10) . "...");
            error_log("DEBUG CSRF - Método: " . $_SERVER['REQUEST_METHOD']);
            error_log("DEBUG CSRF - Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'No referer'));
            
            // Verificar solo que el token exista y coincida
            if (!$sessionToken || !hash_equals($sessionToken, $token)) {
                // Para iframes, ser más permisivo - regenerar token y continuar
                if (isset($_SERVER['HTTP_REFERER']) && 
                    (strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false || 
                     strpos($_SERVER['HTTP_REFERER'], '127.0.0.1') !== false ||
                     strpos($_SERVER['HTTP_REFERER'], 'orion.rinorisk.com') !== false)) {
                    
                    error_log("DEBUG CSRF - Regenerando token para iframe");
                    Session::set('csrf_token', bin2hex(random_bytes(32)));
                } else {
                    // Para uploads de archivos, dar un mensaje más específico
                    if (isset($_FILES) && !empty($_FILES)) {
                        http_response_code(419);
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'CSRF token inválido. Por favor, recarga la página e intenta de nuevo.']);
                    } else {
                        http_response_code(419);
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'CSRF token inválido']);
                    }
                    return;
                }
            }
        }
        
        return $next();
    }
}


