<?php
declare(strict_types=1);

// Front Controller
// ob_start(); // Capture any unexpected output - Commented to prevent JSON issues

// Composer autoload si existe, si no, autoloader propio
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
} else {
    require __DIR__ . '/../spl_autoload.php';
}

$config = require __DIR__ . '/../config/config.php';

// Incluir configuración de límites PHP para archivos grandes
require_once __DIR__ . '/../config/php_limits.php';

// Iniciar sesión segura con configuración extendida para uploads largos
session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => 7200, // 2 horas para uploads largos
    'path' => '/',
    'domain' => '',
    'secure' => $config['session']['cookie_secure'],
    'httponly' => $config['session']['cookie_httponly'],
    'samesite' => $config['session']['cookie_samesite'],
]);

// Configurar parámetros de sesión antes de iniciar
ini_set('session.gc_maxlifetime', 7200); // 2 horas
ini_set('session.cookie_lifetime', 7200); // 2 horas

session_start();

// Seguridad básica de headers
header('X-Content-Type-Options: nosniff');
// header('X-Frame-Options: SAMEORIGIN'); // Comentado para permitir iframes
header('Referrer-Policy: no-referrer-when-downgrade');
header('Content-Security-Policy: ' . $config['security']['csp']);
if (!headers_sent() && !empty($config['security']['hsts'])) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

use App\Helpers\Router;
use App\Helpers\Response;
use App\Middlewares\SecurityHeadersMiddleware;
use App\Middlewares\CsrfMiddleware;
use App\Middlewares\RateLimitMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\JwtAuthMiddleware;
use App\Middlewares\AdminMiddleware;
use App\Controllers\AuthController;
use App\Controllers\DevController;
use App\Controllers\DriveController;
use App\Controllers\ShareController;
use App\Controllers\SharingController;
use App\Controllers\AdminUsersController;
use App\Controllers\AdminGroupsController;

// Manejador especial para enlaces compartidos antes del Router
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path = '/' . ltrim(str_replace($base, '', $uri), '/');
    
    // Normalizar path
    if (function_exists('str_starts_with') && str_starts_with($path, '/index.php/')) {
        $path = '/' . ltrim(substr($path, strlen('/index.php/')), '/');
    } elseif (strpos($path, '/index.php/') === 0) {
        $path = '/' . ltrim(substr($path, strlen('/index.php/')), '/');
    }
    
    // Verificar si es un enlace compartido
    if (preg_match('#^/s/([a-f0-9]{64})$#', $path, $matches)) {
        $token = $matches[1];
        
        // Aplicar middleware de seguridad
        $securityMiddleware = new SecurityHeadersMiddleware();
        $securityMiddleware->handle(function() use ($token) {
            return (new ShareController())->open($token);
        });
        exit;
    }
}

$router = new Router();

// Ruta raíz - Redirección directa al drive (autenticación JWT automática)
$router->add('GET', '/', function () use ($config) {
    header('Location: /biblioteca/public/index.php/drive');
    exit;
}, [SecurityHeadersMiddleware::class]);

// Healthcheck
$router->add('GET', '/health', function () {
    App\Helpers\Response::json(['status' => 'ok', 'time' => time()]);
}, [RateLimitMiddleware::class, SecurityHeadersMiddleware::class]);

// Auth - Solo logout (JWT es el sistema principal)
$router->add('POST', '/auth/logout', [AuthController::class, 'logout'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);

// Dev utilities (local only)
$router->add('POST', '/dev/set-plain-password', [DevController::class, 'setPlainPassword'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class]);
$router->add('GET', '/dev/force-plain-demo', [DevController::class, 'forcePlainDemo'], [SecurityHeadersMiddleware::class]);
$router->add('POST', '/dev/test-upload', [DevController::class, 'testUpload'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/dev/test-user-settings', [DevController::class, 'testUserSettings'], [SecurityHeadersMiddleware::class]);
$router->add('GET', '/dev/test-session', [DevController::class, 'testSession'], [SecurityHeadersMiddleware::class]);

// Drive - Con autenticación JWT (sin CSRF para evitar bloqueos)
$router->add('GET', '/drive', [DriveController::class, 'dashboard'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/upload', [DriveController::class, 'upload'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, RateLimitMiddleware::class]);
$router->add('GET', '/drive/list', [DriveController::class, 'apiList'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/shared-with-me', [DriveController::class, 'sharedWithMe'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/shared-folder-contents', [DriveController::class, 'sharedFolderContents'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/file-info', [DriveController::class, 'getFileInfo'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/download', [DriveController::class, 'downloadFile'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/preview', [DriveController::class, 'previewFile'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/storage-quota', [DriveController::class, 'getStorageQuota'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/folder', [DriveController::class, 'createFolder'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/folder/rename', [DriveController::class, 'renameFolder'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/file/rename', [DriveController::class, 'renameFile'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/folder/move', [DriveController::class, 'moveFolder'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/file/move', [DriveController::class, 'moveFile'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/folder/delete', [DriveController::class, 'deleteFolder'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/file/delete', [DriveController::class, 'deleteFile'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/folder/label', [DriveController::class, 'setFolderLabel'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/folder/icon', [DriveController::class, 'setFolderIcon'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/drive/breadcrumb', [DriveController::class, 'getBreadcrumb'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);

// Background settings - Con autenticación JWT
$router->add('GET', '/drive/background', [DriveController::class, 'getBackgroundSettings'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/background/color', [DriveController::class, 'setBackgroundColor'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/background/image', [DriveController::class, 'setBackgroundImage'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/drive/background/clear', [DriveController::class, 'clearBackground'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);

// Sharing (basic) - Con autenticación JWT
$router->add('POST', '/share/create', [ShareController::class, 'createLink'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/s/{token}', [ShareController::class, 'open'], [SecurityHeadersMiddleware::class]);
$router->add('POST', '/s/{token}', [ShareController::class, 'open'], [SecurityHeadersMiddleware::class]);

// Advanced Sharing System - Con autenticación JWT
$router->add('POST', '/sharing/share-with-users', [SharingController::class, 'shareWithUsers'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/sharing/share-with-group', [SharingController::class, 'shareWithGroup'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/sharing/create-public-link', [SharingController::class, 'createPublicLink'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/sharing/groups', [SharingController::class, 'getAvailableGroups'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/sharing/revoke', [SharingController::class, 'revokeSharing'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/sharing/list/{resourceType}/{resourceId}', [SharingController::class, 'listSharings'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/sharing/my-shares', [SharingController::class, 'getMyShares'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/sharing/update', [SharingController::class, 'updateSharing'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/sharing/remove', [SharingController::class, 'removeSharing'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);

// Nuevas rutas para gestión múltiple de compartidos
$router->add('GET', '/sharing/list-by-resource', [SharingController::class, 'listByResource'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('GET', '/sharing/search-users-groups', [SharingController::class, 'searchUsersGroups'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
$router->add('POST', '/sharing/add-to-resource', [SharingController::class, 'addToResource'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);

// Admin - Gestión de Usuarios (solo para administradores con JWT)
$router->add('GET', '/admin/users', [AdminUsersController::class, 'index'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/users/api', [AdminUsersController::class, 'getUsers'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/create', [AdminUsersController::class, 'createUser'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/update', [AdminUsersController::class, 'updateUser'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/delete', [AdminUsersController::class, 'deleteUser'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/toggle-status', [AdminUsersController::class, 'toggleUserStatus'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/users/search', [AdminUsersController::class, 'searchUsers'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);

// Admin - Gestión de Grupos (solo para administradores con JWT)
$router->add('GET', '/admin/groups/api', [AdminGroupsController::class, 'getGroups'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/create', [AdminGroupsController::class, 'createGroup'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/update', [AdminGroupsController::class, 'updateGroup'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/delete', [AdminGroupsController::class, 'deleteGroup'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/groups/members', [AdminGroupsController::class, 'getGroupMembers'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/groups/available-users', [AdminGroupsController::class, 'getAvailableUsers'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/add-member', [AdminGroupsController::class, 'addMember'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/remove-member', [AdminGroupsController::class, 'removeMember'], [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class, AdminMiddleware::class]);

$router->dispatch();


