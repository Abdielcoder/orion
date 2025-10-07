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
            if (!$sessionToken || !hash_equals($sessionToken, $token)) {
                http_response_code(419);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'CSRF token inválido']);
                return;
            }
        } else {
            if (!Session::get('csrf_token')) {
                Session::set('csrf_token', bin2hex(random_bytes(32)));
            }
        }
        return $next();
    }
}


