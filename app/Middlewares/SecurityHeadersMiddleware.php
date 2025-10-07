<?php

namespace App\Middlewares;

use App\Services\Config;

class SecurityHeadersMiddleware
{
    public function handle(callable $next)
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: no-referrer-when-downgrade');
        
        // CORS headers for AJAX requests with credentials
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        $csp = Config::get('security.csp');
        if ($csp) {
            header('Content-Security-Policy: ' . $csp);
        }
        return $next();
    }
}


