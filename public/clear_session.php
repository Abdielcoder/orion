<?php
/**
 * Página para limpiar completamente la sesión
 */

// Iniciar sesión
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];

// Obtener parámetros de cookies
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destruir la sesión
session_destroy();

// Limpiar todas las cookies relacionadas
$cookies = ['BIBLIO_SESSID', 'PHPSESSID'];
foreach ($cookies as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, '/');
        setcookie($cookie, '', time() - 3600, '/biblioteca/');
        setcookie($cookie, '', time() - 3600, '/biblioteca/public/');
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Limpiada - Biblioteca Digital</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            stroke: white;
            stroke-width: 3;
            fill: none;
        }
        
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }
        
        .info-box {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 15px;
            margin-top: 30px;
            text-align: left;
        }
        
        .info-box h3 {
            color: #92400e;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .info-box ul {
            list-style: none;
            padding-left: 0;
        }
        
        .info-box li {
            color: #78350f;
            font-size: 13px;
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }
        
        .info-box li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #92400e;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1>✅ Sesión Limpiada</h1>
        
        <p>
            Tu sesión ha sido completamente eliminada. Todas las cookies y datos de sesión 
            han sido borrados del servidor.
        </p>
        
        <div class="actions">
            <a href="<?= dirname($_SERVER['PHP_SELF']) ?>/index.php/auth/login" class="btn btn-primary">
                Iniciar Sesión Nuevamente
            </a>
            <button onclick="clearBrowserData()" class="btn btn-secondary">
                Limpiar Caché del Navegador
            </button>
        </div>
        
        <div class="info-box">
            <h3>⚠️ Pasos adicionales recomendados:</h3>
            <ul>
                <li>Cierra todas las pestañas del sitio</li>
                <li>Usa Ctrl+Shift+R (Cmd+Shift+R en Mac) para forzar recarga</li>
                <li>O usa modo incógnito para una sesión completamente nueva</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Limpiar localStorage y sessionStorage
        localStorage.clear();
        sessionStorage.clear();
        
        function clearBrowserData() {
            // Limpiar almacenamiento local
            localStorage.clear();
            sessionStorage.clear();
            
            // Intentar limpiar cookies desde JavaScript
            document.cookie.split(";").forEach(function(c) { 
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
            });
            
            alert('✅ Caché del navegador limpiada.\n\nAhora cierra esta pestaña y abre una nueva para iniciar sesión.');
        }
        
        // Auto-limpiar al cargar la página
        console.log('🧹 Limpiando datos del navegador...');
        localStorage.clear();
        sessionStorage.clear();
        console.log('✅ Datos locales limpiados');
    </script>
</body>
</html>

