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

    public function showLogin()
    {
        $csrf = $_SESSION['csrf_token'] ?? '';
        $baseUrl = $this->getBaseUrl();
        Response::view('auth/login', ['csrf' => $csrf, 'baseUrl' => $baseUrl]);
    }

    private function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . $host . $path . '/index.php';
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            return Response::json(['error' => 'Credenciales requeridas'], 422);
        }
        $usePlain = (bool) Config::get('auth.plaintext_passwords', false);
        if ($usePlain) {
            $user = $this->users->findByEmailAndPasswordPlain($email, $password);
        } else {
            $user = $this->users->findByEmail($email);
            if ($user && $user->password) {
                if (!password_verify($password, $user->password)) {
                    $user = null;
                }
            } else {
                $user = null;
            }
        }

        if (!$user) {
            return Response::json(['error' => 'Email o contraseña inválidos'], 401);
        }
        Session::regenerate();
        Session::set('user_id', $user->id);
        Session::set('user_role', $user->rol);
        return Response::json(['ok' => true]);
    }

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


