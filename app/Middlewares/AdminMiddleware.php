<?php

namespace App\Middlewares;

use App\Services\Session;
use App\Helpers\Response;

class AdminMiddleware
{
    public function handle(callable $next)
    {
        // Verificar que el usuario esté autenticado
        $userId = Session::get('user_id');
        if (!$userId) {
            return Response::json(['error' => 'No autenticado'], 401);
        }

        // Verificar que el usuario sea administrador
        $userRole = Session::get('user_role');
        if ($userRole !== 'administrador') {
            return Response::json(['error' => 'Acceso denegado. Se requieren permisos de administrador.'], 403);
        }

        return $next();
    }
}
