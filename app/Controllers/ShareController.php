<?php

namespace App\Controllers;

use App\Repositories\ShareLinkRepository;
use App\Repositories\FileRepository;
use App\Services\Session;
use App\Helpers\Response;

class ShareController
{
    private ShareLinkRepository $links;
    private FileRepository $files;

    public function __construct()
    {
        $this->links = new ShareLinkRepository();
        $this->files = new FileRepository();
    }

    public function createLink()
    {
        $uid = (int)Session::get('user_id');
        $archivoId = (int)($_POST['archivo_id'] ?? 0);
        $exp = $_POST['expiracion'] ?? null; // formato 'YYYY-MM-DD HH:MM:SS' o null
        if ($archivoId <= 0) {
            return Response::json(['error' => 'Parámetros inválidos'], 422);
        }
        $token = $this->links->create('archivo', $archivoId, $uid, ['read' => true], $exp, 0);
        return Response::json(['ok' => true, 'token' => $token]);
    }

    public function open(string $token)
    {
        // Iniciar sesión al principio
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $link = $this->links->findByToken($token);
        if (!$link) {
            http_response_code(404);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'El enlace que intentas acceder no existe o ha sido eliminado.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            $link = [];
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        if ($link['fecha_expiracion'] && strtotime($link['fecha_expiracion']) < time()) {
            http_response_code(410);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'Este enlace ha expirado y ya no está disponible.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        if ((int)$link['limite_descargas'] > 0 && (int)$link['contador_accesos'] >= (int)$link['limite_descargas']) {
            http_response_code(429);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'Este enlace ha alcanzado su límite máximo de descargas.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        // Verificar autenticación para enlaces protegidos
        if ($link['requiere_password'] && !$this->isLinkAuthenticated($token)) {
            $requiresAuth = false;
            $authError = '';
            
            // Verificar si tiene contraseña hasheada (contraseña tradicional)
            if (!empty($link['password_hash'])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
                    if (password_verify($_POST['password'], $link['password_hash'])) {
                        // Contraseña correcta, marcar como autenticado
                        $this->setLinkAuthenticated($token);
                    } else {
                        $requiresAuth = true;
                        $authError = 'Contraseña incorrecta';
                    }
                } else {
                    $requiresAuth = true;
                }
            }
            // Verificar si tiene código de acceso (almacenado en texto plano en 'contraseña')
            elseif (!empty($link['contraseña'])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['access_code'])) {
                    if (strtoupper(trim($_POST['access_code'])) === strtoupper(trim($link['contraseña']))) {
                        // Código correcto, marcar como autenticado
                        $this->setLinkAuthenticated($token);
                    } else {
                        $requiresAuth = true;
                        $authError = 'Código de acceso incorrecto';
                    }
                } else {
                    $requiresAuth = true;
                }
            }
            
            if ($requiresAuth) {
                $requiresPassword = $requiresAuth; // Para compatibilidad con la vista
                $passwordError = $authError; // Para compatibilidad con la vista
                $hasAccessCode = !empty($link['contraseña']) && empty($link['password_hash']);
                $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
                $file = null;
                $iconClass = '';
                $iconName = '';
                require __DIR__ . '/../Views/share/view.php';
                return;
            }
        }
        
        // Manejar acciones específicas
        $action = $_GET['action'] ?? 'view';
        
        if ($action === 'download') {
            return $this->downloadFile($link);
        }
        
        if ($action === 'preview') {
            return $this->previewFile($link);
        }
        
        // Incrementar contador de accesos solo para vista principal
        $this->links->incrementAccess($token);
        
        // Obtener información del archivo
        $file = $this->files->findById((int)$link['recurso_id']);
        if (!$file) {
            http_response_code(404);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'El archivo asociado a este enlace no existe.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        // Preparar datos para la vista
        $permissions = [
            'can_download' => (bool)($link['puede_descargar'] ?? 1),
            'can_print' => (bool)($link['puede_imprimir'] ?? 1),
            'can_copy' => (bool)($link['puede_copiar'] ?? 1),
        ];
        
        // Determinar icono y clase CSS
        $extension = strtolower($file->extension ?? '');
        $iconData = $this->getFileIcon($extension);
        $iconName = $iconData['icon'];
        $iconClass = $iconData['class'];
        
        // Cargar la vista
        require __DIR__ . '/../Views/share/view.php';
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
    
    private function downloadFile(array $link)
    {
        if (!(bool)($link['puede_descargar'] ?? 1)) {
            http_response_code(403);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'Este enlace no permite descargas.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            $link = [];
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        $file = $this->files->findById((int)$link['recurso_id']);
        if (!$file) {
            http_response_code(404);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'Archivo no encontrado.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            $link = [];
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        $filePath = $file->ruta_local;
        if (!file_exists($filePath)) {
            http_response_code(404);
            $requiresPassword = false;
            $passwordError = '';
            $hasAccessCode = false;
            $errorMessage = 'Archivo físico no encontrado.';
            $permissions = ['can_download' => false, 'can_print' => false, 'can_copy' => false];
            $file = null;
            $iconClass = '';
            $iconName = '';
            $link = [];
            require __DIR__ . '/../Views/share/view.php';
            return;
        }
        
        // Headers para descarga
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file->nombre_original . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        
        // Enviar archivo
        readfile($filePath);
        exit;
    }
    
    private function previewFile(array $link)
    {
        $file = $this->files->findById((int)$link['recurso_id']);
        if (!$file) {
            http_response_code(404);
            return;
        }
        
        $filePath = $file->ruta_local;
        if (!file_exists($filePath)) {
            http_response_code(404);
            return;
        }
        
        $extension = strtolower($file->extension ?? '');
        
        // Solo permitir preview de ciertos tipos de archivo
        $previewableTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];
        if (!in_array($extension, $previewableTypes)) {
            http_response_code(415);
            echo 'Vista previa no disponible para este tipo de archivo';
            return;
        }
        
        // Establecer content-type apropiado
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain'
        ];
        
        header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
    
    private function getFileIcon(string $extension): array
    {
        $icons = [
            'pdf' => ['icon' => 'fas fa-file-pdf', 'class' => 'pdf'],
            'doc' => ['icon' => 'fas fa-file-word', 'class' => 'doc'],
            'docx' => ['icon' => 'fas fa-file-word', 'class' => 'doc'],
            'xls' => ['icon' => 'fas fa-file-excel', 'class' => 'xls'],
            'xlsx' => ['icon' => 'fas fa-file-excel', 'class' => 'xls'],
            'ppt' => ['icon' => 'fas fa-file-powerpoint', 'class' => 'ppt'],
            'pptx' => ['icon' => 'fas fa-file-powerpoint', 'class' => 'ppt'],
            'jpg' => ['icon' => 'fas fa-file-image', 'class' => 'img'],
            'jpeg' => ['icon' => 'fas fa-file-image', 'class' => 'img'],
            'png' => ['icon' => 'fas fa-file-image', 'class' => 'img'],
            'gif' => ['icon' => 'fas fa-file-image', 'class' => 'img'],
            'mp4' => ['icon' => 'fas fa-file-video', 'class' => 'video'],
            'avi' => ['icon' => 'fas fa-file-video', 'class' => 'video'],
            'mov' => ['icon' => 'fas fa-file-video', 'class' => 'video'],
            'mp3' => ['icon' => 'fas fa-file-audio', 'class' => 'audio'],
            'wav' => ['icon' => 'fas fa-file-audio', 'class' => 'audio'],
            'zip' => ['icon' => 'fas fa-file-archive', 'class' => 'zip'],
            'rar' => ['icon' => 'fas fa-file-archive', 'class' => 'zip'],
            'txt' => ['icon' => 'fas fa-file-alt', 'class' => 'txt'],
        ];
        
        return $icons[$extension] ?? ['icon' => 'fas fa-file', 'class' => 'default'];
    }
    
    /**
     * Verificar si un enlace está autenticado en la sesión actual
     */
    private function isLinkAuthenticated(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionKey = 'authenticated_links';
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }
        
        // Verificar si el token está autenticado y no ha expirado
        if (isset($_SESSION[$sessionKey][$token])) {
            $authTime = $_SESSION[$sessionKey][$token];
            
            // Considerar autenticado por 2 horas
            if (time() - $authTime < 7200) {
                return true;
            } else {
                // Limpiar token expirado
                unset($_SESSION[$sessionKey][$token]);
            }
        }
        
        return false;
    }
    
    /**
     * Marcar un enlace como autenticado en la sesión
     */
    private function setLinkAuthenticated(string $token): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionKey = 'authenticated_links';
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }
        
        $_SESSION[$sessionKey][$token] = time();
        
        // Limpiar tokens antiguos (más de 2 horas)
        $now = time();
        foreach ($_SESSION[$sessionKey] as $t => $authTime) {
            if ($now - $authTime >= 7200) {
                unset($_SESSION[$sessionKey][$t]);
            }
        }
    }
}


