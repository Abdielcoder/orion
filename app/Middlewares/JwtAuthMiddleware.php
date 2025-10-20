<?php

namespace App\Middlewares;

use App\Services\Session;
use App\Helpers\JwtHelper;
use App\Repositories\UserRepository;

/**
 * Middleware para autenticación mediante JWT desde header x-token
 * Este middleware reemplaza el sistema de login tradicional
 */
class JwtAuthMiddleware
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    
    public function handle(callable $next)
    {
        // LIMPIAR TODO: Eliminar cookies, sesiones, cache
        $this->clearAllAuth();
        
        // Obtener token del header x-token
        $token = $this->getTokenFromHeader();
        
        if (!$token) {
            error_log("JWT Auth - No se encontró token en header x-token");
            
            // BYPASS TEMPORAL: Intentar obtener usuario del contexto de Orion
            // TODO: Remover cuando Orion envíe el token correctamente
            $bypassMode = true; // Cambiar a false cuando JWT esté funcionando desde Orion
            
            if ($bypassMode) {
                // Intentar obtener email del usuario desde parámetros GET o POST
                $userEmail = $this->getUserEmailFromContext();
                
                if ($userEmail) {
                    // Buscar usuario específico por email
                    $user = $this->userRepository->findByEmail($userEmail);
                    
                    if ($user && $user->activo) {
                        Session::regenerate();
                        Session::set('user_id', $user->id);
                        Session::set('user_role', $user->rol);
                        Session::set('jwt_validated', true);
                        Session::set('jwt_email', $user->email);
                        Session::set('jwt_bypass', true);
                        
                        error_log("JWT Auth - BYPASS ACTIVO: Usando usuario específico: {$user->email} (ID: {$user->id}, Rol: {$user->rol})");
                        return $next();
                    } else {
                        error_log("JWT Auth - BYPASS: Usuario no encontrado o inactivo: {$userEmail}");
                    }
                }
                
                // Fallback: usar usuario administrador por defecto
                $stmt = \App\Services\Database::connection()->prepare("SELECT id, email, rol FROM usuarios WHERE rol = 'administrador' AND activo = 1 LIMIT 1");
                $stmt->execute();
                $defaultUser = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($defaultUser) {
                    $userId = (int)$defaultUser['id'];
                    $userRole = $defaultUser['rol'];
                    $userEmail = $defaultUser['email'];
                    
                    Session::regenerate();
                    Session::set('user_id', $userId);
                    Session::set('user_role', $userRole);
                    Session::set('jwt_validated', true);
                    Session::set('jwt_email', $userEmail);
                    Session::set('jwt_bypass', true);
                    
                    error_log("JWT Auth - BYPASS ACTIVO: Usando usuario administrador por defecto: {$userEmail} (ID: {$userId})");
                    return $next();
                }
            }
            
            return $this->unauthorized('Token no proporcionado');
        }
        
        error_log("JWT Auth - Token recibido: " . substr($token, 0, 50) . "...");
        
        // Decodificar JWT
        $payload = JwtHelper::decode($token);
        
        if (!$payload) {
            error_log("JWT Auth - No se pudo decodificar el token");
            return $this->unauthorized('Token inválido');
        }
        
        // Extraer email del payload
        $email = JwtHelper::getEmail($payload);
        
        if (!$email) {
            error_log("JWT Auth - No se encontró email en el payload del token");
            return $this->unauthorized('Token sin email');
        }
        
        error_log("JWT Auth - Email extraído del token: {$email}");
        
        // Buscar usuario en la base de datos
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            error_log("JWT Auth - Usuario no encontrado en BD: {$email}");
            return $this->unauthorized('Usuario no autorizado - Email no existe en biblioteca digital');
        }
        
        if (!$user->activo) {
            error_log("JWT Auth - Usuario inactivo: {$email}");
            return $this->unauthorized('Usuario no activo');
        }
        
        error_log("JWT Auth - Usuario autenticado: {$user->email} (ID: {$user->id}, Rol: {$user->rol})");
        
        // ESTABLECER SESIÓN LIMPIA con la lógica de negocio existente del usuario
        Session::regenerate();
        Session::set('user_id', $user->id);
        Session::set('user_role', $user->rol);
        Session::set('jwt_validated', true);
        Session::set('jwt_email', $email);
        
        // Actualizar último acceso
        $this->userRepository->updateLastAccess($user->id);
        
        error_log("JWT Auth - Usuario autenticado con lógica de biblioteca: {$user->email} (ID: {$user->id}, Rol: {$user->rol}, Activo: {$user->activo})");
        
        return $next();
    }
    
    /**
     * Intenta obtener el email del usuario desde el contexto de la petición
     * (parámetros GET, POST, headers, etc.)
     * 
     * @return string|null
     */
    private function getUserEmailFromContext(): ?string
    {
        // 1. Buscar en parámetros GET
        if (isset($_GET['user_email']) && !empty($_GET['user_email'])) {
            $email = trim($_GET['user_email']);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("JWT Auth - Email encontrado en GET: {$email}");
                return $email;
            }
        }
        
        // 2. Buscar en parámetros POST
        if (isset($_POST['user_email']) && !empty($_POST['user_email'])) {
            $email = trim($_POST['user_email']);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("JWT Auth - Email encontrado en POST: {$email}");
                return $email;
            }
        }
        
        // 3. Buscar en headers personalizados
        $headers = [
            'HTTP_X_USER_EMAIL',
            'HTTP_X-USER-EMAIL',
            'X-User-Email',
            'X-USER-EMAIL',
            'x-user-email'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $email = trim($_SERVER[$header]);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    error_log("JWT Auth - Email encontrado en header: {$header} = {$email}");
                    return $email;
                }
            }
        }
        
        // 4. Buscar en getallheaders()
        if (function_exists('getallheaders')) {
            $allHeaders = getallheaders();
            if ($allHeaders) {
                foreach ($allHeaders as $name => $value) {
                    if (strtolower($name) === 'x-user-email' && !empty($value)) {
                        $email = trim($value);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            error_log("JWT Auth - Email encontrado en getallheaders(): {$name} = {$email}");
                            return $email;
                        }
                    }
                }
            }
        }
        
        // 5. Buscar en apache_request_headers()
        if (function_exists('apache_request_headers')) {
            $allHeaders = apache_request_headers();
            if ($allHeaders) {
                foreach ($allHeaders as $name => $value) {
                    if (strtolower($name) === 'x-user-email' && !empty($value)) {
                        $email = trim($value);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            error_log("JWT Auth - Email encontrado en apache_request_headers(): {$name} = {$email}");
                            return $email;
                        }
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Obtiene el token JWT del header x-token o de parámetros GET/POST
     * 
     * @return string|null
     */
    private function getTokenFromHeader(): ?string
    {
        // 1. Intentar obtener desde localStorage/cookie del navegador
        // (El token enviado por JavaScript desde el cliente)
        if (isset($_COOKIE['orion_jwt_token']) && !empty($_COOKIE['orion_jwt_token'])) {
            error_log("JWT Auth - Token encontrado en cookie: orion_jwt_token");
            return trim($_COOKIE['orion_jwt_token']);
        }
        
        // 2. Intentar varios formatos de header
        $headers = [
            'HTTP_X_TOKEN',
            'HTTP_X-TOKEN',
            'X-Token',
            'X-TOKEN',
            'x-token'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                error_log("JWT Auth - Token encontrado en header: {$header}");
                return trim($_SERVER[$header]);
            }
        }
        
        // 3. Intentar obtener de getallheaders() si está disponible
        if (function_exists('getallheaders')) {
            $allHeaders = getallheaders();
            if ($allHeaders) {
                foreach ($allHeaders as $name => $value) {
                    if (strtolower($name) === 'x-token' && !empty($value)) {
                        error_log("JWT Auth - Token encontrado en getallheaders(): {$name}");
                        return trim($value);
                    }
                }
            }
        }
        
        // 4. Fallback: buscar en apache_request_headers() si está disponible
        if (function_exists('apache_request_headers')) {
            $allHeaders = apache_request_headers();
            if ($allHeaders) {
                foreach ($allHeaders as $name => $value) {
                    if (strtolower($name) === 'x-token' && !empty($value)) {
                        error_log("JWT Auth - Token encontrado en apache_request_headers(): {$name}");
                        return trim($value);
                    }
                }
            }
        }
        
        // 5. Intentar obtener desde parámetros GET
        if (isset($_GET['jwt_token']) && !empty($_GET['jwt_token'])) {
            error_log("JWT Auth - Token encontrado en parámetro GET: jwt_token");
            return trim($_GET['jwt_token']);
        }
        
        // 6. Intentar obtener desde parámetros POST
        if (isset($_POST['jwt_token']) && !empty($_POST['jwt_token'])) {
            error_log("JWT Auth - Token encontrado en parámetro POST: jwt_token");
            return trim($_POST['jwt_token']);
        }
        
        return null;
    }
    
    /**
     * Limpiar todas las autenticaciones, cookies, sesiones y cache
     */
    private function clearAllAuth(): void
    {
        // Limpiar sesión PHP
        $_SESSION = [];
        
        // Destruir cookies de sesión
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, 
                $params['path'], $params['domain'], 
                $params['secure'], $params['httponly']);
        }
        
        // Destruir sesión actual
        session_destroy();
        
        // Limpiar headers de cache
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Limpiar variables de sesión específicas
        unset($_SESSION['user_id']);
        unset($_SESSION['user_role']);
        unset($_SESSION['jwt_validated']);
        unset($_SESSION['jwt_email']);
        unset($_SESSION['jwt_bypass']);
        
        error_log("JWT Auth - Limpieza completa de autenticación realizada");
    }
    
    /**
     * Respuesta de no autorizado
     * 
     * @param string $message Mensaje de error
     */
    private function unauthorized(string $message): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'No autorizado',
            'message' => $message
        ]);
    }
}

