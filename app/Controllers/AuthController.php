<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Services\Session;
use App\Services\Config;
use App\Helpers\Response;

class AuthController
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    // Métodos de login eliminados - Solo JWT ahora

    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        return Response::json(['ok' => true]);
    }
}


