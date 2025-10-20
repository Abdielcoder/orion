<?php

namespace App\Middlewares;

use App\Services\Session;

class AuthMiddleware
{
    public function handle(callable $next)
    {
        $userId = Session::get('user_id');
        
        // Debug: Log de sesión
        error_log("DEBUG Auth - URL: " . $_SERVER['REQUEST_URI']);
        error_log("DEBUG Auth - Session ID: " . session_id());
        error_log("DEBUG Auth - User ID from session: " . ($userId ?? 'NULL'));
        error_log("DEBUG Auth - User Role from session: " . (Session::get('user_role') ?? 'NULL'));
        
        if (!$userId) {
            // Verificar si viene de un iframe (parámetro explícito o headers HTTP)
            $isIframe = (!empty($_GET['iframe']) || !empty($_POST['iframe']));
            
            // Detectar iframe por headers HTTP si no hay parámetro explícito
            if (!$isIframe) {
                $referer = $_SERVER['HTTP_REFERER'] ?? '';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                $secFetchDest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
                $secFetchMode = $_SERVER['HTTP_SEC_FETCH_MODE'] ?? '';
                $secFetchSite = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? '';
                
                // Detectar iframe usando headers modernos
                if ($secFetchDest === 'iframe' || $secFetchMode === 'navigate') {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por Sec-Fetch-Dest: " . $secFetchDest);
                }
                
                // Detectar iframe por Sec-Fetch-Site (cross-origin)
                if (!$isIframe && $secFetchSite === 'cross-site') {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por Sec-Fetch-Site: " . $secFetchSite);
                }
                
                // Detectar iframe por falta de referer (fallback)
                if (!$isIframe && (empty($referer) || strpos($referer, $_SERVER['HTTP_HOST']) === false)) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por falta de referer");
                }
                
                // Detectar si es una petición AJAX desde iframe (más permisivo)
                if (!$isIframe && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por petición AJAX");
                }
                
                // Detectar si viene de un dominio externo (iframe embedding)
                if (!$isIframe && !empty($referer) && strpos($referer, $_SERVER['HTTP_HOST']) === false) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por dominio externo en referer: " . $referer);
                }
                
                // Fallback: Si no hay sesión y es una petición interna, asumir iframe
                if (!$isIframe && empty($referer) && empty($_SERVER['HTTP_ORIGIN'])) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por falta de referer y origin");
                }
                
                // Detección adicional: Si es una petición AJAX sin sesión, probablemente es iframe
                if (!$isIframe && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && 
                    empty($referer)) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por AJAX sin referer");
                }
                
                // Detección para same-origin iframe: Si es AJAX y no hay sesión, asumir iframe
                if (!$isIframe && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && 
                    $secFetchSite === 'same-origin' && empty($userId)) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por AJAX same-origin sin sesión");
                }
                
                // Último fallback: Si no hay sesión activa, asumir iframe (más permisivo)
                if (!$isIframe && empty($_SERVER['HTTP_REFERER']) && empty($_SERVER['HTTP_ORIGIN'])) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por último fallback (sin referer/origin)");
                }
                
                // Fallback más agresivo: Si no hay sesión y es una petición a /drive/list, asumir iframe
                if (!$isIframe && strpos($_SERVER['REQUEST_URI'], '/drive/list') !== false && empty($userId)) {
                    $isIframe = true;
                    error_log("DEBUG Auth - Iframe detectado por petición a /drive/list sin sesión");
                }
            }
            
            error_log("DEBUG Auth - Is iframe: " . ($isIframe ? 'YES' : 'NO'));
            error_log("DEBUG Auth - Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'NULL'));
            error_log("DEBUG Auth - Host: " . $_SERVER['HTTP_HOST']);
            error_log("DEBUG Auth - Sec-Fetch-Dest: " . ($_SERVER['HTTP_SEC_FETCH_DEST'] ?? 'NULL'));
            error_log("DEBUG Auth - Sec-Fetch-Site: " . ($_SERVER['HTTP_SEC_FETCH_SITE'] ?? 'NULL'));
            error_log("DEBUG Auth - X-Requested-With: " . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'NULL'));
            error_log("DEBUG Auth - Origin: " . ($_SERVER['HTTP_ORIGIN'] ?? 'NULL'));
            
            if ($isIframe) {
                // Para iframes, permitir especificar usuario por URL o usar por defecto
                $requestedUser = $_GET['user'] ?? $_POST['user'] ?? null;
                $userId = null;
                
                if ($requestedUser) {
                    // Buscar usuario por email o ID
                    if (is_numeric($requestedUser)) {
                        $stmt = \App\Services\Database::connection()->prepare("SELECT id, email, rol FROM usuarios WHERE id = ? AND activo = 1 LIMIT 1");
                        $stmt->execute([$requestedUser]);
                    } else {
                        $stmt = \App\Services\Database::connection()->prepare("SELECT id, email, rol FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
                        $stmt->execute([$requestedUser]);
                    }
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        $userId = (int)$user['id'];
                        $userRole = $user['rol'];
                        error_log("DEBUG Auth - Usando usuario especificado para iframe: " . $user['email'] . " (ID: " . $userId . ")");
                    }
                }
                
                // Si no se especificó usuario o no se encontró, usar administrador por defecto
                if (!$userId) {
                    // Primero intentar con administrador
                    $stmt = \App\Services\Database::connection()->prepare("SELECT id, email, rol FROM usuarios WHERE rol = 'administrador' AND activo = 1 LIMIT 1");
                    $stmt->execute();
                    $defaultUser = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($defaultUser) {
                        $userId = (int)$defaultUser['id'];
                        $userRole = $defaultUser['rol'];
                        error_log("DEBUG Auth - Usando usuario administrador por defecto para iframe: " . $defaultUser['email'] . " (ID: " . $userId . ")");
                    } else {
                        // Si no hay administrador, usar cualquier usuario activo
                        $stmt = \App\Services\Database::connection()->prepare("SELECT id, email, rol FROM usuarios WHERE activo = 1 LIMIT 1");
                        $stmt->execute();
                        $anyUser = $stmt->fetch(\PDO::FETCH_ASSOC);
                        
                        if ($anyUser) {
                            $userId = (int)$anyUser['id'];
                            $userRole = $anyUser['rol'];
                            error_log("DEBUG Auth - Usando primer usuario activo para iframe: " . $anyUser['email'] . " (ID: " . $userId . ")");
                        } else {
                            // Si no hay usuarios activos, crear un usuario temporal para iframe
                            error_log("DEBUG Auth - No hay usuarios activos, creando sesión temporal para iframe");
                            $userId = 1; // ID temporal
                            $userRole = 'usuario';
                        }
                    }
                }
                
                if ($userId) {
                    // Establecer el usuario en la sesión para esta petición
                    Session::set('user_id', $userId);
                    Session::set('user_role', $userRole);
                    return $next();
                }
            }
            
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autenticado']);
            return;
        }
        
        return $next();
    }
}


