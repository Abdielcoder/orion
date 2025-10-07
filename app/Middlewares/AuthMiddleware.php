<?php

namespace App\Middlewares;

use App\Services\Session;

class AuthMiddleware
{
    public function handle(callable $next)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autenticado']);
            return;
        }
        return $next();
    }
}


