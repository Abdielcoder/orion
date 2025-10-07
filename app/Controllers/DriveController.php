<?php

namespace App\Controllers;

use App\Repositories\FolderRepository;
use App\Repositories\FileRepository;
use App\Repositories\UserSettingsRepository;
use App\Services\Session;
use App\Services\Storage;
use App\Services\Database;
use App\Models\FileItem;
use App\Helpers\Response;
use Exception;

class DriveController
{
    private FolderRepository $folders;
    private FileRepository $files;
    private UserSettingsRepository $userSettings;

    public function __construct()
    {
        $this->folders = new FolderRepository();
        $this->files = new FileRepository();
        $this->userSettings = new UserSettingsRepository();
    }

    public function dashboard()
    {
        $uid = (int)Session::get('user_id');
        $userRole = Session::get('user_role', 'viewer');
        $parent = isset($_GET['folder']) ? (int)$_GET['folder'] : null;
        $folders = $this->folders->listByParent($parent, $uid);
        $files = $this->files->listByFolder($parent ?? 0, $uid);
        
        // Obtener información del usuario
        $stmt = Database::connection()->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
        $stmt->execute([$uid]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
        $userName = $userData['nombre'] ?? $userData['email'] ?? 'Usuario';
        
        // Obtener configuraciones de fondo del usuario
        $backgroundSettings = $this->userSettings->getBackgroundSettings($uid);
        
        Response::view('drive/dashboard', [
            'folders' => $folders,
            'files' => $files,
            'uid' => $uid,
            'userName' => $userName,
            'userRole' => $userRole,
            'parent' => $parent ?: 0, // Asegurar que sea 0 en lugar de null para JS
            'csrf' => $_SESSION['csrf_token'] ?? '',
            'backgroundSettings' => $backgroundSettings,
        ]);
    }

    public function apiList()
    {
        $uid = (int)Session::get('user_id');
        $parent = isset($_GET['folder']) ? (int)$_GET['folder'] : null;
        $folders = $this->folders->listByParent($parent, $uid);
        $files = $this->files->listByFolder($parent ?? 0, $uid);
        return Response::json([
            'folders' => array_map(fn($f)=>['id'=>$f->id,'nombre'=>$f->nombre,'etiqueta'=>$f->etiqueta,'color_etiqueta'=>$f->color_etiqueta,'icono_personalizado'=>$f->icono_personalizado], $folders),
            'files' => array_map(fn($f)=>['id'=>$f->id,'nombre'=>$f->nombre,'mime'=>$f->tipo_mime], $files),
        ]);
    }
    
    public function sharedWithMe()
    {
        $uid = (int)Session::get('user_id');
        
        try {
            // Obtener archivos compartidos conmigo desde permisos_recursos
            $stmt = \App\Services\Database::connection()->prepare("
                SELECT 
                    a.id, a.nombre, a.extension, a.tipo_mime, a.tamaño,
                    pr.permiso as permission,
                    pr.fecha_creacion as shared_date,
                    u.nombre as owner_name,
                    u.email as owner_email,
                    'file' as type
                FROM permisos_recursos pr
                JOIN archivos a ON pr.recurso_id = a.id AND pr.recurso_tipo = 'archivo'
                JOIN usuarios u ON a.propietario_id = u.id
                WHERE pr.usuario_id = ? AND pr.activo = 1
                AND (pr.fecha_expiracion IS NULL OR pr.fecha_expiracion > NOW())
                
                UNION ALL
                
                SELECT 
                    c.id, c.nombre, null as extension, null as tipo_mime, null as tamaño,
                    pr.permiso as permission,
                    pr.fecha_creacion as shared_date,
                    u.nombre as owner_name,
                    u.email as owner_email,
                    'folder' as type
                FROM permisos_recursos pr
                JOIN carpetas c ON pr.recurso_id = c.id AND pr.recurso_tipo = 'carpeta'
                JOIN usuarios u ON c.propietario_id = u.id
                WHERE pr.usuario_id = ? AND pr.activo = 1
                AND (pr.fecha_expiracion IS NULL OR pr.fecha_expiracion > NOW())
                
                ORDER BY shared_date DESC
            ");
            
            $stmt->execute([$uid, $uid]);
            $sharedItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return Response::json([
                'ok' => true,
                'files' => $sharedItems
            ]);
            
        } catch (Exception $e) {
            error_log('Error getting shared files: ' . $e->getMessage());
            return Response::json([
                'ok' => false,
                'error' => 'Error al obtener archivos compartidos'
            ], 500);
        }
    }

    public function sharedFolderContents()
    {
        $uid = (int)Session::get('user_id');
        $folderId = (int)($_GET['folder_id'] ?? 0);
        
        if (!$folderId) {
            return Response::json(['error' => 'ID de carpeta requerido'], 422);
        }

        try {
            // Verificar si el usuario tiene permisos para acceder a esta carpeta
            $stmt = Database::connection()->prepare("
                SELECT pr.permiso, c.nombre as folder_name, c.propietario_id
                FROM permisos_recursos pr
                JOIN carpetas c ON pr.recurso_id = c.id 
                WHERE pr.recurso_id = ? AND pr.usuario_id = ? AND pr.recurso_tipo = 'carpeta' 
                AND pr.activo = 1 AND (pr.fecha_expiracion IS NULL OR pr.fecha_expiracion > NOW())
            ");
            $stmt->execute([$folderId, $uid]);
            $permission = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$permission) {
                return Response::json(['error' => 'No tienes permisos para acceder a esta carpeta'], 403);
            }

            // Obtener subcarpetas de la carpeta compartida
            $folders = $this->folders->listAllByParent($folderId);
            
            // Obtener archivos de la carpeta compartida (sin filtrar por propietario)
            $stmt = Database::connection()->prepare("
                SELECT * FROM archivos 
                WHERE carpeta_id = ? AND activo = 1 
                ORDER BY nombre
            ");
            $stmt->execute([$folderId]);
            $fileRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $files = [];
            foreach ($fileRows as $row) {
                $file = new FileItem();
                $file->id = (int)$row['id'];
                $file->nombre = $row['nombre'];
                $file->tipo_mime = $row['tipo_mime'];
                $file->extension = $row['extension'];
                $file->tamaño = $row['tamaño'] ? (int)$row['tamaño'] : null;
                $files[] = $file;
            }

            return Response::json([
                'ok' => true,
                'folder_id' => $folderId,
                'folder_name' => $permission['folder_name'],
                'permission' => $permission['permiso'],
                'is_owner' => $permission['propietario_id'] === $uid,
                'folders' => array_map(fn($f) => [
                    'id' => $f->id,
                    'nombre' => $f->nombre,
                    'etiqueta' => $f->etiqueta,
                    'color_etiqueta' => $f->color_etiqueta,
                    'icono_personalizado' => $f->icono_personalizado
                ], $folders),
                'files' => array_map(fn($f) => [
                    'id' => $f->id,
                    'nombre' => $f->nombre,
                    'mime' => $f->tipo_mime
                ], $files),
            ]);

        } catch (Exception $e) {
            error_log('Error getting shared folder contents: ' . $e->getMessage());
            return Response::json([
                'ok' => false,
                'error' => 'Error al obtener contenido de la carpeta'
            ], 500);
        }
    }

    public function getFileInfo()
    {
        $uid = (int)Session::get('user_id');
        $fileId = (int)($_GET['id'] ?? 0);
        
        if (!$fileId) {
            return Response::json(['error' => 'ID de archivo requerido'], 422);
        }

        try {
            // Obtener información del archivo
            $stmt = Database::connection()->prepare("
                SELECT a.*, u.nombre as propietario_nombre
                FROM archivos a
                JOIN usuarios u ON a.propietario_id = u.id
                WHERE a.id = ? AND a.activo = 1
            ");
            $stmt->execute([$fileId]);
            $fileData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$fileData) {
                return Response::json(['error' => 'Archivo no encontrado'], 404);
            }

            // Verificar permisos - debe ser propietario o tener permisos compartidos
            $hasAccess = false;
            
            // Check if user owns the file
            if ($fileData['propietario_id'] == $uid) {
                $hasAccess = true;
            } else {
                // Check if file is shared with user
                $stmt = Database::connection()->prepare("
                    SELECT COUNT(*) as count FROM permisos_recursos 
                    WHERE recurso_id = ? AND usuario_id = ? AND recurso_tipo = 'archivo' 
                    AND activo = 1 AND (fecha_expiracion IS NULL OR fecha_expiracion > NOW())
                ");
                $stmt->execute([$fileId, $uid]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $hasAccess = $result['count'] > 0;
            }

            if (!$hasAccess) {
                return Response::json(['error' => 'No tienes permisos para acceder a este archivo'], 403);
            }

            return Response::json([
                'ok' => true,
                'file' => [
                    'id' => (int)$fileData['id'],
                    'nombre' => $fileData['nombre'],
                    'nombre_original' => $fileData['nombre_original'],
                    'tipo_mime' => $fileData['tipo_mime'],
                    'extension' => $fileData['extension'],
                    'tamaño' => $fileData['tamaño'] ? (int)$fileData['tamaño'] : null,
                    'fecha_creacion' => $fileData['fecha_creacion'],
                    'fecha_modificacion' => $fileData['fecha_modificacion'],
                    'propietario_id' => (int)$fileData['propietario_id'],
                    'propietario_nombre' => $fileData['propietario_nombre'],
                    'carpeta_id' => $fileData['carpeta_id'] ? (int)$fileData['carpeta_id'] : null,
                    'version' => (int)$fileData['version']
                ]
            ]);

        } catch (Exception $e) {
            error_log('Error getting file info: ' . $e->getMessage());
            return Response::json([
                'ok' => false,
                'error' => 'Error al obtener información del archivo'
            ], 500);
        }
    }

    public function downloadFile()
    {
        $uid = (int)Session::get('user_id');
        $fileId = (int)($_GET['id'] ?? 0);
        
        if (!$fileId) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        try {
            // Obtener información del archivo
            $stmt = Database::connection()->prepare("
                SELECT a.*, u.nombre as propietario_nombre
                FROM archivos a
                JOIN usuarios u ON a.propietario_id = u.id
                WHERE a.id = ? AND a.activo = 1
            ");
            $stmt->execute([$fileId]);
            $fileData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$fileData) {
                http_response_code(404);
                echo 'Archivo no encontrado';
                return;
            }

            // Verificar permisos
            $hasAccess = false;
            
            if ($fileData['propietario_id'] == $uid) {
                $hasAccess = true;
            } else {
                // Check if file is shared with user
                $stmt = Database::connection()->prepare("
                    SELECT COUNT(*) as count FROM permisos_recursos 
                    WHERE recurso_id = ? AND usuario_id = ? AND recurso_tipo = 'archivo' 
                    AND activo = 1 AND (fecha_expiracion IS NULL OR fecha_expiracion > NOW())
                ");
                $stmt->execute([$fileId, $uid]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $hasAccess = $result['count'] > 0;
            }

            if (!$hasAccess) {
                http_response_code(403);
                echo 'No tienes permisos para acceder a este archivo';
                return;
            }

            // Construir ruta del archivo
            $filePath = $fileData['ruta_local'];
            
            if (!file_exists($filePath)) {
                http_response_code(404);
                echo 'Archivo físico no encontrado';
                return;
            }

            // Configurar headers para DESCARGA (siempre attachment)
            $mimeType = $fileData['tipo_mime'] ?: 'application/octet-stream';
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($filePath));
            
            // SIEMPRE forzar descarga
            header('Content-Disposition: attachment; filename="' . ($fileData['nombre_original'] ?: $fileData['nombre']) . '"');
            
            header('Cache-Control: private, max-age=3600');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type');
            
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Enviar archivo
            readfile($filePath);

        } catch (Exception $e) {
            error_log('Error downloading file: ' . $e->getMessage());
            http_response_code(500);
            echo 'Error interno del servidor';
        }
    }

    public function previewFile()
    {
        $uid = (int)Session::get('user_id');
        $fileId = (int)($_GET['id'] ?? 0);
        
        if (!$fileId) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        try {
            // Obtener información del archivo
            $stmt = Database::connection()->prepare("
                SELECT a.*, u.nombre as propietario_nombre
                FROM archivos a
                JOIN usuarios u ON a.propietario_id = u.id
                WHERE a.id = ? AND a.activo = 1
            ");
            $stmt->execute([$fileId]);
            $fileData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$fileData) {
                http_response_code(404);
                echo 'Archivo no encontrado';
                return;
            }

            // Verificar permisos
            $hasAccess = false;
            
            if ($fileData['propietario_id'] == $uid) {
                $hasAccess = true;
            } else {
                // Check if file is shared with user
                $stmt = Database::connection()->prepare("
                    SELECT COUNT(*) as count FROM permisos_recursos 
                    WHERE recurso_id = ? AND usuario_id = ? AND recurso_tipo = 'archivo' 
                    AND activo = 1 AND (fecha_expiracion IS NULL OR fecha_expiracion > NOW())
                ");
                $stmt->execute([$fileId, $uid]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $hasAccess = $result['count'] > 0;
            }

            if (!$hasAccess) {
                http_response_code(403);
                echo 'No tienes permisos para acceder a este archivo';
                return;
            }

            // Construir ruta del archivo
            $filePath = $fileData['ruta_local'];
            
            if (!file_exists($filePath)) {
                http_response_code(404);
                echo 'Archivo físico no encontrado';
                return;
            }

            // Configurar headers para VISTA PREVIA (inline, no descarga)
            $mimeType = $fileData['tipo_mime'] ?: 'application/octet-stream';
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($filePath));
            
            // SIEMPRE inline para vista previa
            header('Content-Disposition: inline; filename="' . ($fileData['nombre_original'] ?: $fileData['nombre']) . '"');
            
            header('Cache-Control: private, max-age=3600');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type');
            
            // Limpiar cualquier output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Enviar archivo
            readfile($filePath);

        } catch (Exception $e) {
            error_log('Error previewing file: ' . $e->getMessage());
            http_response_code(500);
            echo 'Error interno del servidor';
        }
    }

    public function createFolder()
    {
        $uid = (int)Session::get('user_id');
        $name = trim($_POST['name'] ?? '');
        $parent = isset($_POST['parent']) && $_POST['parent'] !== '' ? (int)$_POST['parent'] : null;
        if ($name === '') { 
            return Response::json(['error' => 'Nombre requerido'], 422); 
        }
        try {
            $id = $this->folders->create($name, $parent, $uid);
            return Response::json(['ok' => true, 'id' => $id]);
        } catch (Exception $e) {
            return Response::json(['error' => 'Error al crear carpeta: ' . $e->getMessage()], 500);
        }
    }

    public function renameFolder()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id <= 0 || $name === '') { return Response::json(['error' => 'Parámetros inválidos'], 422); }
        
        // Verificar propiedad
        if (!$this->folders->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Carpeta no encontrada o sin permisos'], 403);
        }
        
        $this->folders->rename($id, $name, $uid);
        return Response::json(['ok' => true]);
    }

    public function renameFile()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id <= 0 || $name === '') { return Response::json(['error' => 'Parámetros inválidos'], 422); }
        
        // Verificar propiedad
        if (!$this->files->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Archivo no encontrado o sin permisos'], 403);
        }
        
        $this->files->rename($id, $name, $uid);
        return Response::json(['ok' => true]);
    }

    public function moveFolder()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        $parent = isset($_POST['parent']) ? (int)$_POST['parent'] : null;
        if ($id <= 0) { return Response::json(['error' => 'ID inválido'], 422); }
        
        // Verificar propiedad de la carpeta a mover
        if (!$this->folders->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Carpeta no encontrada o sin permisos'], 403);
        }
        
        // Verificar propiedad de la carpeta destino (si no es null)
        if ($parent !== null && !$this->folders->findByIdAndOwner($parent, $uid)) {
            return Response::json(['error' => 'Carpeta destino no encontrada o sin permisos'], 403);
        }
        
        $this->folders->move($id, $parent, $uid);
        return Response::json(['ok' => true]);
    }

    public function moveFile()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        $folderIdRaw = $_POST['folder_id'] ?? 0;
        $folderId = is_numeric($folderIdRaw) ? (int)$folderIdRaw : 0;
        if ($id <= 0 || $folderId < 0) { return Response::json(['error' => 'Parámetros inválidos'], 422); }
        
        // Verificar propiedad del archivo
        if (!$this->files->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Archivo no encontrado o sin permisos'], 403);
        }
        
        // Verificar propiedad de la carpeta destino
        if ($folderId > 0 && !$this->folders->findByIdAndOwner($folderId, $uid)) {
            return Response::json(['error' => 'Carpeta destino no encontrada o sin permisos'], 403);
        }
        
        // Si folderId es 0, mover a raíz => carpeta_id NULL
        $this->files->move($id, $folderId === 0 ? null : $folderId, $uid);
        return Response::json(['ok' => true]);
    }

    public function deleteFolder()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { return Response::json(['error' => 'ID inválido'], 422); }
        
        // Verificar propiedad
        if (!$this->folders->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Carpeta no encontrada o sin permisos'], 403);
        }
        
        $this->folders->delete($id, $uid);
        return Response::json(['ok' => true]);
    }

    public function deleteFile()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { return Response::json(['error' => 'ID inválido'], 422); }
        
        // Verificar propiedad
        if (!$this->files->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Archivo no encontrado o sin permisos'], 403);
        }
        
        $this->files->delete($id, $uid);
        return Response::json(['ok' => true]);
    }

    public function getBreadcrumb()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { return Response::json(['breadcrumb' => []]); }
        $breadcrumb = $this->folders->getBreadcrumb($id);
        return Response::json(['breadcrumb' => $breadcrumb]);
    }

    public function setFolderLabel()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        $etiqueta = trim($_POST['etiqueta'] ?? '') ?: null;
        $color = trim($_POST['color'] ?? '') ?: null;
        
        if ($id <= 0) { 
            return Response::json(['error' => 'ID inválido'], 422); 
        }
        
        // Verificar propiedad
        if (!$this->folders->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Carpeta no encontrada o sin permisos'], 403);
        }
        
        // Validar color hex
        if ($color && !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return Response::json(['error' => 'Color inválido'], 422);
        }
        
        try {
            $this->folders->setLabel($id, $etiqueta, $color);
            return Response::json(['ok' => true]);
        } catch (Exception $e) {
            return Response::json(['error' => 'Error al establecer etiqueta: ' . $e->getMessage()], 500);
        }
    }

    public function setFolderIcon()
    {
        $uid = (int)Session::get('user_id');
        $id = (int)($_POST['id'] ?? 0);
        $icono = trim($_POST['icono'] ?? '') ?: null;
        
        if ($id <= 0) { 
            return Response::json(['error' => 'ID inválido'], 422); 
        }
        
        // Verificar propiedad
        if (!$this->folders->findByIdAndOwner($id, $uid)) {
            return Response::json(['error' => 'Carpeta no encontrada o sin permisos'], 403);
        }
        
        // Validar que el icono sea una clase de Font Awesome válida
        if ($icono && !preg_match('/^fa[sr]?\s+fa-[\w-]+$/', $icono)) {
            return Response::json(['error' => 'Icono inválido'], 422);
        }
        
        try {
            $this->folders->setIcon($id, $icono);
            return Response::json(['ok' => true]);
        } catch (Exception $e) {
            return Response::json(['error' => 'Error al establecer icono: ' . $e->getMessage()], 500);
        }
    }

    public function upload()
    {
        try {
            // Incluir configuración de límites PHP
            require_once __DIR__ . '/../../config/php_limits.php';
            
            error_log('Upload started');
            $uid = (int)Session::get('user_id');
            $folderId = (int)($_POST['folder_id'] ?? 0);
            error_log('UID: ' . $uid . ', FolderID: ' . $folderId);
            
            if (!isset($_FILES['file'])) {
                return Response::json(['error' => 'Archivo no recibido'], 400);
            }
            
            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMsg = match($file['error']) {
                    UPLOAD_ERR_INI_SIZE => 'Archivo muy grande (límite PHP)',
                    UPLOAD_ERR_FORM_SIZE => 'Archivo muy grande (límite formulario)', 
                    UPLOAD_ERR_PARTIAL => 'Carga parcial',
                    UPLOAD_ERR_NO_FILE => 'No se recibió archivo',
                    UPLOAD_ERR_NO_TMP_DIR => 'Directorio temporal faltante',
                    UPLOAD_ERR_CANT_WRITE => 'Error de escritura',
                    UPLOAD_ERR_EXTENSION => 'Extensión bloqueada',
                    default => 'Error de carga desconocido'
                };
                return Response::json(['error' => $errorMsg], 400);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileSize = $file['size'];
            error_log('File extension: ' . $ext . ', Size: ' . $fileSize);
            
            // Log para debugging de archivos grandes
            if ($fileSize > 100 * 1024 * 1024) { // > 100MB
                error_log('Large file upload detected: ' . $file['name'] . ' (' . $this->formatBytes($fileSize) . ')');
            }

            // Verificar cuota de almacenamiento del usuario
            $stmt = Database::connection()->prepare("
                SELECT cuota_almacenamiento, almacenamiento_usado 
                FROM usuarios 
                WHERE id = ? AND activo = 1
            ");
            $stmt->execute([$uid]);
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$userData) {
                return Response::json(['error' => 'Usuario no encontrado'], 404);
            }

            $cuotaDisponible = $userData['cuota_almacenamiento'] - $userData['almacenamiento_usado'];
            
            if ($fileSize > $cuotaDisponible) {
                $cuotaFormateada = $this->formatBytes($userData['cuota_almacenamiento']);
                $usadoFormateado = $this->formatBytes($userData['almacenamiento_usado']);
                $disponibleFormateado = $this->formatBytes($cuotaDisponible);
                $archivoFormateado = $this->formatBytes($fileSize);
                
                return Response::json([
                    'error' => "No tienes suficiente espacio disponible. Necesitas {$archivoFormateado} pero solo tienes {$disponibleFormateado} disponibles de tu cuota de {$cuotaFormateada}."
                ], 413); // 413 Request Entity Too Large
            }
            
            // Simple MIME detection
            $mime = 'application/octet-stream';
            if (function_exists('mime_content_type')) {
                $mime = @mime_content_type($file['tmp_name']) ?: 'application/octet-stream';
            } elseif (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = @finfo_file($finfo, $file['tmp_name']) ?: 'application/octet-stream';
                finfo_close($finfo);
            }
            error_log('MIME type: ' . $mime);
            
            $size = (int)$file['size'];
            $safeName = bin2hex(random_bytes(16)) . ($ext ? ('.' . $ext) : '');
            $destDir = Storage::userPath($uid);
            $dest = $destDir . '/' . $safeName;
            
            error_log('Destination: ' . $dest);
            error_log('Temp file: ' . $file['tmp_name']);
            
            if (!@move_uploaded_file($file['tmp_name'], $dest)) {
                error_log('Failed to move uploaded file');
                return Response::json(['error' => 'No se pudo mover el archivo'], 500);
            }
            
            error_log('File moved successfully');
            
            $item = new FileItem();
            $item->nombre = $file['name'];
            $item->nombre_original = $file['name'];
            $item->tipo_mime = $mime;
            $item->extension = $ext ?: null;
            $item->tamaño = $size;
            // Si folderId es 0 (raíz), usar NULL
            if ($folderId <= 0) {
                $item->carpeta_id = null; // Raíz (NULL)
                error_log('Using root folder (NULL) for upload');
            } else {
                $item->carpeta_id = $folderId;
            }
            $item->propietario_id = $uid;
            $item->ruta_local = $dest;
            
            error_log('Creating file item in DB');
            $id = $this->files->create($item);
            error_log('File created with ID: ' . $id);

            // Actualizar el uso de almacenamiento del usuario
            $stmt = Database::connection()->prepare("
                UPDATE usuarios 
                SET almacenamiento_usado = almacenamiento_usado + ? 
                WHERE id = ?
            ");
            $stmt->execute([$fileSize, $uid]);
            error_log('Updated storage usage for user ' . $uid . ' with ' . $fileSize . ' bytes');
            
            return Response::json(['ok' => true, 'id' => $id]);
            
        } catch (Exception $e) {
            error_log('Upload error: ' . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // Endpoints para configuración de fondo por usuario
    public function getBackgroundSettings()
    {
        $uid = (int)Session::get('user_id');
        $settings = $this->userSettings->getBackgroundSettings($uid);
        return Response::json(['settings' => $settings]);
    }

    public function setBackgroundColor()
    {
        $uid = (int)Session::get('user_id');
        $color = trim($_POST['color'] ?? '');
        
        if (!$color || !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return Response::json(['error' => 'Color inválido'], 422);
        }
        
        try {
            $this->userSettings->setBackgroundColor($uid, $color);
            return Response::json(['ok' => true]);
        } catch (Exception $e) {
            return Response::json(['error' => 'Error al guardar configuración'], 500);
        }
    }

    public function setBackgroundImage()
    {
        $uid = (int)Session::get('user_id');
        $imageData = $_POST['image_data'] ?? '';
        
        if (!$imageData || !str_starts_with($imageData, 'data:image/')) {
            return Response::json(['error' => 'Imagen inválida'], 422);
        }
        
        // Validar tamaño (máximo 5MB en base64 - más generoso)
        if (strlen($imageData) > 5 * 1024 * 1024) {
            return Response::json(['error' => 'Imagen demasiado grande (máximo 5MB)'], 422);
        }
        
        try {
            $this->userSettings->setBackgroundImage($uid, $imageData);
            return Response::json(['ok' => true]);
        } catch (Exception $e) {
            error_log("Error en setBackgroundImage: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return Response::json([
                'error' => 'Error al guardar configuración',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    public function clearBackground()
    {
        $uid = (int)Session::get('user_id');
        
        try {
            $this->userSettings->clearBackground($uid);
            return Response::json(['ok' => true]);
        } catch (Exception $e) {
            error_log("Error en clearBackground: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return Response::json([
                'error' => 'Error al limpiar configuración',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de cuota de almacenamiento del usuario
     */
    public function getStorageQuota()
    {
        $uid = (int)Session::get('user_id');
        
        try {
            $stmt = Database::connection()->prepare("
                SELECT cuota_almacenamiento, almacenamiento_usado 
                FROM usuarios 
                WHERE id = ? AND activo = 1
            ");
            $stmt->execute([$uid]);
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$userData) {
                return Response::json(['error' => 'Usuario no encontrado'], 404);
            }

            // Recalcular el uso actual desde la base de datos
            $stmt = Database::connection()->prepare("
                SELECT COALESCE(SUM(tamaño), 0) as total_usado
                FROM archivos 
                WHERE propietario_id = ? AND activo = 1
            ");
            $stmt->execute([$uid]);
            $usageData = $stmt->fetch(\PDO::FETCH_ASSOC);
            $actualUsage = (int)$usageData['total_usado'];

            // Actualizar el cache de uso si es diferente
            if ($actualUsage != $userData['almacenamiento_usado']) {
                $stmt = Database::connection()->prepare("
                    UPDATE usuarios 
                    SET almacenamiento_usado = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$actualUsage, $uid]);
            }

            return Response::json([
                'quota' => (int)$userData['cuota_almacenamiento'],
                'used' => $actualUsage,
                'available' => max(0, (int)$userData['cuota_almacenamiento'] - $actualUsage),
                'percentage' => round(($actualUsage / (int)$userData['cuota_almacenamiento']) * 100, 2)
            ]);

        } catch (Exception $e) {
            error_log('Error getting storage quota: ' . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Formatear bytes a unidades legibles
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}


