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
header('X-Frame-Options: SAMEORIGIN');
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

// Rutas mínimas - Redirección directa al login
$router->add('GET', '/', function () use ($config) {
    if (!empty($_SESSION['user_id'])) {
        header('Location: /biblioteca/public/index.php/drive');
        exit;
    }
    // Redirigir directamente al login
    header('Location: /biblioteca/public/index.php/auth/login');
    exit;
}, [SecurityHeadersMiddleware::class]);

// Healthcheck
$router->add('GET', '/health', function () {
    App\Helpers\Response::json(['status' => 'ok', 'time' => time()]);
}, [RateLimitMiddleware::class, SecurityHeadersMiddleware::class]);

// Auth
$router->add('GET', '/auth/login', [AuthController::class, 'showLogin'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class]);
$router->add('POST', '/auth/login', [AuthController::class, 'login'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, RateLimitMiddleware::class]);
$router->add('POST', '/auth/logout', [AuthController::class, 'logout'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);

// Dev utilities (local only)
$router->add('POST', '/dev/set-plain-password', [DevController::class, 'setPlainPassword'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class]);
$router->add('GET', '/dev/force-plain-demo', [DevController::class, 'forcePlainDemo'], [SecurityHeadersMiddleware::class]);
$router->add('POST', '/dev/test-upload', [DevController::class, 'testUpload'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/dev/test-user-settings', [DevController::class, 'testUserSettings'], [SecurityHeadersMiddleware::class]);
$router->add('GET', '/dev/test-session', [DevController::class, 'testSession'], [SecurityHeadersMiddleware::class]);

// Drive
$router->add('GET', '/drive', [DriveController::class, 'dashboard'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/upload', [DriveController::class, 'upload'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, RateLimitMiddleware::class]);
$router->add('GET', '/drive/list', [DriveController::class, 'apiList'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/shared-with-me', [DriveController::class, 'sharedWithMe'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/shared-folder-contents', [DriveController::class, 'sharedFolderContents'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/file-info', [DriveController::class, 'getFileInfo'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/download', [DriveController::class, 'downloadFile'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/preview', [DriveController::class, 'previewFile'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/storage-quota', [DriveController::class, 'getStorageQuota'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/folder', [DriveController::class, 'createFolder'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/folder/rename', [DriveController::class, 'renameFolder'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/file/rename', [DriveController::class, 'renameFile'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/folder/move', [DriveController::class, 'moveFolder'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/file/move', [DriveController::class, 'moveFile'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/folder/delete', [DriveController::class, 'deleteFolder'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/file/delete', [DriveController::class, 'deleteFile'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/folder/label', [DriveController::class, 'setFolderLabel'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/folder/icon', [DriveController::class, 'setFolderIcon'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/drive/breadcrumb', [DriveController::class, 'getBreadcrumb'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);

// Background settings
$router->add('GET', '/drive/background', [DriveController::class, 'getBackgroundSettings'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/background/color', [DriveController::class, 'setBackgroundColor'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/background/image', [DriveController::class, 'setBackgroundImage'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/drive/background/clear', [DriveController::class, 'clearBackground'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);

// Sharing (basic)
$router->add('POST', '/share/create', [ShareController::class, 'createLink'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/s/{token}', [ShareController::class, 'open'], [SecurityHeadersMiddleware::class]);
$router->add('POST', '/s/{token}', [ShareController::class, 'open'], [SecurityHeadersMiddleware::class]);

// Advanced Sharing System
$router->add('POST', '/sharing/share-with-users', [SharingController::class, 'shareWithUsers'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/sharing/share-with-group', [SharingController::class, 'shareWithGroup'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/sharing/create-public-link', [SharingController::class, 'createPublicLink'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/sharing/groups', [SharingController::class, 'getAvailableGroups'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('POST', '/sharing/revoke', [SharingController::class, 'revokeSharing'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
$router->add('GET', '/sharing/list/{resourceType}/{resourceId}', [SharingController::class, 'listSharings'], [SecurityHeadersMiddleware::class, AuthMiddleware::class]);

// Admin - Gestión de Usuarios (solo para administradores)
$router->add('GET', '/admin/users', [AdminUsersController::class, 'index'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/users/api', [AdminUsersController::class, 'getUsers'], [SecurityHeadersMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/create', [AdminUsersController::class, 'createUser'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/update', [AdminUsersController::class, 'updateUser'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/delete', [AdminUsersController::class, 'deleteUser'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/users/toggle-status', [AdminUsersController::class, 'toggleUserStatus'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/users/search', [AdminUsersController::class, 'searchUsers'], [SecurityHeadersMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);

// Admin - Gestión de Grupos (solo para administradores)
$router->add('GET', '/admin/groups/api', [AdminGroupsController::class, 'getGroups'], [SecurityHeadersMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/create', [AdminGroupsController::class, 'createGroup'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/update', [AdminGroupsController::class, 'updateGroup'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/delete', [AdminGroupsController::class, 'deleteGroup'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('GET', '/admin/groups/members', [AdminGroupsController::class, 'getGroupMembers'], [SecurityHeadersMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/add-member', [AdminGroupsController::class, 'addMember'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);
$router->add('POST', '/admin/groups/remove-member', [AdminGroupsController::class, 'removeMember'], [SecurityHeadersMiddleware::class, CsrfMiddleware::class, AuthMiddleware::class, AdminMiddleware::class]);

$router->dispatch();


