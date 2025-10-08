<?php

namespace App\Middlewares;

use App\Services\Config;
use App\Services\Session;

class CsrfMiddleware
{
    public function handle(callable $next)
    {
        $tokenName = Config::get('security.csrf_token_name', '_csrf');
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $token = $_POST[$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            $sessionToken = Session::get('csrf_token');
            
            // Verificar solo que el token exista y coincida (sin verificar timestamp)
            // El token solo expira cuando la sesión expira
            if (!$sessionToken || !hash_equals($sessionToken, $token)) {
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
        } else {
            // Generar token si no existe
            if (!Session::get('csrf_token')) {
                Session::set('csrf_token', bin2hex(random_bytes(32)));
            }
        }
        return $next();
    }
}


