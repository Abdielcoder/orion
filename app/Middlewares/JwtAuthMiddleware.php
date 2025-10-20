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
            
            // BYPASS TEMPORAL: Si no hay token, usar usuario administrador por defecto
            // TODO: Remover cuando Orion envíe el token correctamente
            $bypassMode = true; // Cambiar a false cuando JWT esté funcionando desde Orion
            
            if ($bypassMode) {
                // Buscar usuario administrador por defecto
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
     * Obtiene el token JWT del header x-token
     * 
     * @return string|null
     */
    private function getTokenFromHeader(): ?string
    {
        // Intentar varios formatos de header
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
        
        // Intentar obtener de getallheaders() si está disponible
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
        
        // Fallback: buscar en apache_request_headers() si está disponible
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

