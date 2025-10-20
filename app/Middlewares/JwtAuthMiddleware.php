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
        // Verificar si ya hay una sesión activa (para evitar validar JWT en cada request)
        $userId = Session::get('user_id');
        $jwtValidated = Session::get('jwt_validated');
        
        if ($userId && $jwtValidated) {
            error_log("JWT Auth - Sesión activa para usuario ID: {$userId}");
            return $next();
        }
        
        // Obtener token del header x-token
        $token = $this->getTokenFromHeader();
        
        if (!$token) {
            error_log("JWT Auth - No se encontró token en header x-token");
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
        
        // Establecer sesión con la lógica de negocio existente del usuario
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
    
    // Método de auto-creación de usuarios eliminado
    // Solo se permite acceso a usuarios existentes en la base de datos
    
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

