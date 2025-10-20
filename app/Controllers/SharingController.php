<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Models\User;
use App\Services\Session;
use App\Helpers\Response;
use App\Services\Database;

class SharingController
{
    private UserRepository $userRepository;
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private Database $db;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->fileRepository = new FileRepository();
        $this->folderRepository = new FolderRepository();
        $this->db = new Database();
    }


    /**
     * Compartir un archivo con usuarios específicos
     */
    public function shareWithUsers()
    {
        $userId = (int)Session::get('user_id');
        $resourceType = $_POST['resource_type'] ?? ''; // 'archivo' o 'carpeta'
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        $userEmails = $_POST['user_emails'] ?? []; // Array de emails
        $permission = $_POST['permission'] ?? 'lector';
        $expiryDate = $_POST['expiry_date'] ?? null;
        $canDownload = (int)($_POST['can_download'] ?? 1);
        $canPrint = (int)($_POST['can_print'] ?? 1);
        $canCopy = (int)($_POST['can_copy'] ?? 1);
        $notifyChanges = (int)($_POST['notify_changes'] ?? 0);
        $message = $_POST['message'] ?? '';

        // Validaciones
        if (!in_array($resourceType, ['archivo', 'carpeta'])) {
            return Response::json(['error' => 'Tipo de recurso inválido'], 422);
        }

        if (!$resourceId || empty($userEmails)) {
            return Response::json(['error' => 'Datos requeridos faltantes'], 422);
        }

        if (!$this->canUserShareResource($userId, $resourceType, $resourceId)) {
            return Response::json(['error' => 'No tienes permisos para compartir este recurso'], 403);
        }

        try {
            Database::connection()->beginTransaction();

            $sharedCount = 0;
            $errors = [];

            foreach ($userEmails as $email) {
                $targetUser = $this->userRepository->findByEmail(trim($email));
                if (!$targetUser) {
                    $errors[] = "Usuario no encontrado: $email";
                    continue;
                }

                if ($targetUser->id == $userId) {
                    $errors[] = "No puedes compartir contigo mismo: $email";
                    continue;
                }

                // Verificar si ya existe un permiso
                $permisosTable = $resourceType === 'archivo' ? 'permisos_archivos' : 'permisos_carpetas';
                $resourceIdColumn = $resourceType === 'archivo' ? 'archivo_id' : 'carpeta_id';
                $stmt = Database::connection()->prepare("
                    SELECT id FROM {$permisosTable} 
                    WHERE {$resourceIdColumn} = ? AND usuario_id = ? AND activo = 1
                ");
                $stmt->execute([$resourceId, $targetUser->id]);
                $existingPermission = $stmt->fetch();

                if ($existingPermission) {
                    // Actualizar permiso existente
                    $stmt = Database::connection()->prepare("
                        UPDATE {$permisosTable} 
                        SET permiso = ?, fecha_expiracion = ?, puede_descargar = ?, 
                            puede_imprimir = ?, puede_copiar = ?, notificar_cambios = ?, 
                            fecha_otorgado = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $permission, $expiryDate, $canDownload, $canPrint, 
                        $canCopy, $notifyChanges, $existingPermission['id']
                    ]);
                } else {
                    // Crear nuevo permiso
                    $stmt = Database::connection()->prepare("
                        INSERT INTO {$permisosTable} 
                        ({$resourceIdColumn}, tipo_comparticion, usuario_id, permiso, fecha_expiracion, 
                         puede_descargar, puede_imprimir, puede_copiar, notificar_cambios,
                         otorgado_por, activo, fecha_otorgado)
                        VALUES (?, 'usuario', ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
                    ");
                    $stmt->execute([
                        $resourceId, $targetUser->id, $permission, $expiryDate,
                        $canDownload, $canPrint, $canCopy, $notifyChanges, $userId
                    ]);
                }

                // Enviar notificación
                $this->sendSharingNotification($targetUser->id, $resourceType, $resourceId, $permission, $message);
                $sharedCount++;
            }

            Database::connection()->commit();

            $response = ['success' => true, 'shared_count' => $sharedCount];
            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }

            return Response::json($response);

        } catch (\Exception $e) {
            Database::connection()->rollBack();
            error_log("Error compartiendo con usuarios: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Compartir un archivo/carpeta con un grupo
     */
    public function shareWithGroup()
    {
        $userId = (int)Session::get('user_id');
        $resourceType = $_POST['resource_type'] ?? '';
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        $groupId = (int)($_POST['group_id'] ?? 0);
        $permission = $_POST['permission'] ?? 'lector';
        $expiryDate = $_POST['expiry_date'] ?? null;
        $canDownload = (int)($_POST['can_download'] ?? 1);
        $canPrint = (int)($_POST['can_print'] ?? 1);
        $canCopy = (int)($_POST['can_copy'] ?? 1);
        $notifyChanges = (int)($_POST['notify_changes'] ?? 0);

        // Validaciones
        if (!in_array($resourceType, ['archivo', 'carpeta']) || !$resourceId || !$groupId) {
            return Response::json(['error' => 'Datos requeridos faltantes'], 422);
        }

        if (!$this->canUserShareResource($userId, $resourceType, $resourceId)) {
            return Response::json(['error' => 'No tienes permisos para compartir este recurso'], 403);
        }

        try {
            Database::connection()->beginTransaction();

            // Verificar que el grupo existe
            $stmt = Database::connection()->prepare("
                SELECT id, nombre FROM grupos 
                WHERE id = ? AND activo = 1
            ");
            $stmt->execute([$groupId]);
            $group = $stmt->fetch();

            if (!$group) {
                Database::connection()->rollBack();
                return Response::json(['error' => 'Grupo no encontrado'], 404);
            }

            // Determinar tabla según tipo de recurso
            $table = $resourceType === 'archivo' ? 'permisos_archivos' : 'permisos_carpetas';
            $columnId = $resourceType === 'archivo' ? 'archivo_id' : 'carpeta_id';
            
            // Crear permiso para el grupo
            $stmt = Database::connection()->prepare("
                INSERT INTO {$table}
                ({$columnId}, usuario_id, tipo_comparticion, grupo_id, permiso, fecha_expiracion, 
                 puede_descargar, puede_imprimir, puede_copiar, notificar_cambios, 
                 otorgado_por, activo)
                VALUES (?, 0, 'grupo', ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            
            $stmt->execute([
                $resourceId, $groupId, $permission, $expiryDate,
                $canDownload, $canPrint, $canCopy, $notifyChanges, $userId
            ]);

            Database::connection()->commit();

            return Response::json([
                'success' => true,
                'message' => "Recurso compartido exitosamente con el grupo {$group['nombre']}"
            ]);

        } catch (\Exception $e) {
            Database::connection()->rollBack();
            error_log("Error compartiendo con grupo: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Generar código de acceso de 6 caracteres alfanuméricos
     */
    private function generateAccessCode(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    /**
     * Crear enlace de compartición público
     */
    public function createPublicLink()
    {
        $userId = (int)Session::get('user_id');
        $resourceType = $_POST['resource_type'] ?? '';
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        $permission = $_POST['permission'] ?? 'lector';
        $expiryDate = $_POST['expiry_date'] ?? null;
        $requiresAuth = (int)($_POST['requires_auth'] ?? 0);
        $allowedDomains = $_POST['allowed_domains'] ?? '';
        $canDownload = (int)($_POST['can_download'] ?? 1);
        $canPrint = (int)($_POST['can_print'] ?? 1);
        $canCopy = (int)($_POST['can_copy'] ?? 1);
        $notifyAccess = (int)($_POST['notify_access'] ?? 0);
        $password = $_POST['password'] ?? '';
        $useAccessCode = (int)($_POST['use_access_code'] ?? 0);

        // Validaciones
        if (!in_array($resourceType, ['archivo', 'carpeta']) || !$resourceId) {
            return Response::json(['error' => 'Datos requeridos faltantes'], 422);
        }

        if (!$this->canUserShareResource($userId, $resourceType, $resourceId)) {
            return Response::json(['error' => 'No tienes permisos para compartir este recurso'], 403);
        }

        try {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $passwordHash = $password ? password_hash($password, PASSWORD_DEFAULT) : null;
            
            // Generar código de acceso si se solicita
            $accessCode = $useAccessCode ? $this->generateAccessCode() : null;
            
            // Obtener nombre del recurso
            $resourceName = '';
            if ($resourceType === 'archivo') {
                $stmt = Database::connection()->prepare("SELECT nombre FROM archivos WHERE id = ?");
                $stmt->execute([$resourceId]);
                $result = $stmt->fetch();
                $resourceName = $result ? $result['nombre'] : 'Archivo';
            } else {
                $stmt = Database::connection()->prepare("SELECT nombre FROM carpetas WHERE id = ?");
                $stmt->execute([$resourceId]);
                $result = $stmt->fetch();
                $resourceName = $result ? $result['nombre'] : 'Carpeta';
            }

            // Verificar qué columnas existen en la tabla
            $stmt = Database::connection()->prepare("SHOW COLUMNS FROM enlaces_compartidos");
            $stmt->execute();
            $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            // Construir consulta dinámicamente basada en columnas existentes
            $insertColumns = ['token'];
            $insertValues = [$token];
            $placeholders = ['?'];
            
            // Mapeo de columnas y valores
            $columnMapping = [
                'tipo' => $resourceType,
                'recurso_tipo' => $resourceType,
                'recurso_id' => $resourceId,
                'creado_por' => $userId,
                'propietario_id' => $userId,
                'nombre_recurso' => $resourceName,
                'rol_acceso' => $permission,
                'nivel_acceso' => $this->mapPermissionToAccessLevel($permission),
                'permiso' => $permission,
                'fecha_expiracion' => $expiryDate,
                'requiere_autenticacion' => $requiresAuth,
                'dominios_permitidos' => $allowedDomains,
                'puede_descargar' => $canDownload,
                'puede_imprimir' => $canPrint,
                'puede_copiar' => $canCopy,
                'notificar_accesos' => $notifyAccess,
                'requiere_password' => ($password || $accessCode) ? 1 : 0,
                'password' => $passwordHash,
                'password_hash' => $passwordHash,
                'contraseña' => $accessCode,
                'activo' => 1
            ];
            
            foreach ($columnMapping as $column => $value) {
                if (in_array($column, $columns)) {
                    $insertColumns[] = $column;
                    $insertValues[] = $value;
                    $placeholders[] = '?';
                }
            }
            
            $sql = sprintf(
                "INSERT INTO enlaces_compartidos (%s) VALUES (%s)",
                implode(', ', $insertColumns),
                implode(', ', $placeholders)
            );
            
            $stmt = Database::connection()->prepare($sql);
            $stmt->execute($insertValues);

            $linkId = Database::connection()->lastInsertId();
            
            // Construir URL base desde configuración o usar default
            $baseUrl = $_SERVER['HTTP_HOST'] ?? 'localhost:8888';
            $publicUrl = "http://{$baseUrl}/biblioteca/public/index.php/s/{$token}";

            return Response::json([
                'success' => true,
                'message' => 'Enlace público creado exitosamente',
                'link_id' => $linkId,
                'token' => $token,
                'url' => $publicUrl,
                'permission' => $this->getPermissionLabel($permission),
                'expires' => $expiryDate,
                'requires_password' => (bool)$password,
                'access_code' => $accessCode,
                'requires_access_code' => (bool)$accessCode
            ]);

        } catch (\Exception $e) {
            error_log("Error creando enlace público: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return Response::json([
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage() // Solo en desarrollo, quitar en producción
            ], 500);
        }
    }
    
    /**
     * Mapear permiso a nivel de acceso para compatibilidad
     */
    private function mapPermissionToAccessLevel($permission)
    {
        $mapping = [
            'lector' => 'ver',
            'comentarista' => 'ver',
            'editor' => 'editar',
            'propietario' => 'editar'
        ];
        return $mapping[$permission] ?? 'ver';
    }
    
    /**
     * Obtener etiqueta legible del permiso
     */
    private function getPermissionLabel($permission)
    {
        $labels = [
            'lector' => 'Lector',
            'comentarista' => 'Comentarista',
            'editor' => 'Editor',
            'propietario' => 'Propietario'
        ];
        return $labels[$permission] ?? 'Lector';
    }

    /**
     * Obtener permisos de un recurso
     */
    public function getResourcePermissions()
    {
        $resourceType = $_GET['resource_type'] ?? '';
        $resourceId = (int)($_GET['resource_id'] ?? 0);
        $userId = (int)Session::get('user_id');

        if (!in_array($resourceType, ['archivo', 'carpeta']) || !$resourceId) {
            return Response::json(['error' => 'Parámetros inválidos'], 422);
        }

        try {
            $stmt = Database::connection()->prepare("
                SELECT 
                    pr.id,
                    pr.usuario_id,
                    pr.grupo_id,
                    pr.permiso,
                    pr.fecha_expiracion,
                    pr.puede_descargar,
                    pr.puede_imprimir,
                    pr.puede_copiar,
                    pr.notificar_cambios,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email,
                    g.nombre as grupo_nombre
                FROM permisos_recursos pr
                LEFT JOIN usuarios u ON pr.usuario_id = u.id
                LEFT JOIN grupos g ON pr.grupo_id = g.id
                WHERE pr.recurso_tipo = ? AND pr.recurso_id = ? AND pr.activo = 1
                ORDER BY pr.fecha_creacion DESC
            ");
            $stmt->execute([$resourceType, $resourceId]);
            $permissions = $stmt->fetchAll();

            return Response::json([
                'success' => true,
                'permissions' => $permissions
            ]);

        } catch (\Exception $e) {
            error_log("Error obteniendo permisos: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener grupos disponibles para compartir
     */
    public function getAvailableGroups()
    {
        $userId = (int)Session::get('user_id');

        try {
            $stmt = Database::connection()->prepare("
                SELECT DISTINCT g.id, g.nombre, g.descripcion
                FROM grupos g
                INNER JOIN grupo_miembros gm ON g.id = gm.grupo_id
                WHERE gm.usuario_id = ? AND g.activo = 1
                ORDER BY g.nombre
            ");
            $stmt->execute([$userId]);
            $groups = $stmt->fetchAll();

            return Response::json([
                'success' => true,
                'groups' => $groups
            ]);

        } catch (\Exception $e) {
            error_log("Error obteniendo grupos: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Revocar compartición
     */
    public function revokeSharing()
    {
        $permissionId = (int)($_POST['permission_id'] ?? 0);
        $userId = (int)Session::get('user_id');

        if (!$permissionId) {
            return Response::json(['error' => 'ID de permiso requerido'], 422);
        }

        try {
            // Verificar que el usuario tenga permisos para revocar
            $stmt = Database::connection()->prepare("
                SELECT pr.*, a.propietario_id as archivo_propietario, c.propietario_id as carpeta_propietario
                FROM permisos_recursos pr
                LEFT JOIN archivos a ON pr.recurso_tipo = 'archivo' AND pr.recurso_id = a.id
                LEFT JOIN carpetas c ON pr.recurso_tipo = 'carpeta' AND pr.recurso_id = c.id
                WHERE pr.id = ?
            ");
            $stmt->execute([$permissionId]);
            $permission = $stmt->fetch();

            if (!$permission) {
                return Response::json(['error' => 'Permiso no encontrado'], 404);
            }

            $ownerId = $permission['archivo_propietario'] ?? $permission['carpeta_propietario'];
            if ($ownerId != $userId) {
                return Response::json(['error' => 'No tienes permisos para revocar esta compartición'], 403);
            }

            // Revocar permiso (marcar como inactivo)
            $stmt = Database::connection()->prepare("
                UPDATE permisos_recursos 
                SET activo = 0, fecha_actualizacion = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$permissionId]);

            return Response::json([
                'success' => true,
                'message' => 'Compartición revocada exitosamente'
            ]);

        } catch (\Exception $e) {
            error_log("Error revocando compartición: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Listar comparticiones de un recurso
     */
    /**
     * Verificar si un usuario puede compartir un recurso
     */
    private function canUserShareResource($userId, $resourceType, $resourceId): bool
    {
        try {
            if ($resourceType === 'archivo') {
                $stmt = Database::connection()->prepare("
                    SELECT propietario_id FROM archivos 
                    WHERE id = ? AND propietario_id = ? AND activo = 1
                ");
            } else {
                $stmt = Database::connection()->prepare("
                    SELECT propietario_id FROM carpetas 
                    WHERE id = ? AND propietario_id = ? AND activa = 1
                ");
            }
            $stmt->execute([$resourceId, $userId]);
            return (bool)$stmt->fetch();

        } catch (\Exception $e) {
            error_log("Error verificando permisos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación de compartición
     */
    private function sendSharingNotification($userId, $resourceType, $resourceId, $permission, $message)
    {
        $title = "Nuevo recurso compartido contigo";
        $messageText = $message ?: "Se ha compartido un {$resourceType} contigo con permisos de {$permission}";

        $stmt = Database::connection()->prepare("
            INSERT INTO notificaciones 
            (usuario_id, tipo, titulo, mensaje, recurso_tipo, recurso_id)
            VALUES (?, 'comparticion', ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $title, $messageText, $resourceType, $resourceId]);
    }

    public function listSharings($resourceType, $resourceId)
    {
        $userId = (int)Session::get('user_id');
        $resourceId = (int)$resourceId;

        if (!in_array($resourceType, ['archivo', 'carpeta']) || !$resourceId) {
            return Response::json(['error' => 'Parámetros inválidos'], 422);
        }

        if (!$this->canUserShareResource($userId, $resourceType, $resourceId)) {
            return Response::json(['error' => 'No tienes permisos para ver las comparticiones'], 403);
        }

        try {
            $stmt = Database::connection()->prepare("
                SELECT 
                    pr.id,
                    pr.usuario_id,
                    pr.grupo_id,
                    pr.permiso,
                    pr.fecha_expiracion,
                    pr.fecha_creacion,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email,
                    g.nombre as grupo_nombre
                FROM permisos_recursos pr
                LEFT JOIN usuarios u ON pr.usuario_id = u.id
                LEFT JOIN grupos g ON pr.grupo_id = g.id
                WHERE pr.recurso_tipo = ? AND pr.recurso_id = ? AND pr.activo = 1
                ORDER BY pr.fecha_creacion DESC
            ");
            $stmt->execute([$resourceType, $resourceId]);
            $sharings = $stmt->fetchAll();

            return Response::json([
                'success' => true,
                'sharings' => $sharings
            ]);

        } catch (\Exception $e) {
            error_log("Error listando comparticiones: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener todos los recursos compartidos POR el usuario actual
     */
    public function getMyShares()
    {
        $userId = (int)Session::get('user_id');
        
        try {
            // Obtener archivos compartidos por el usuario
            $stmtFiles = Database::connection()->prepare("
                SELECT 
                    pa.id as share_id,
                    'archivo' as resource_type,
                    a.id as resource_id,
                    a.nombre as resource_name,
                    a.extension,
                    a.tipo_mime,
                    a.tamaño,
                    pa.tipo_comparticion,
                    pa.permiso,
                    pa.fecha_otorgado as shared_date,
                    pa.fecha_expiracion as expiry_date,
                    pa.puede_descargar,
                    pa.puede_imprimir,
                    pa.puede_copiar,
                    pa.notificar_cambios,
                    CASE 
                        WHEN pa.tipo_comparticion = 'usuario' THEN u.email
                        WHEN pa.tipo_comparticion = 'grupo' THEN g.nombre
                        ELSE NULL
                    END as shared_with_name,
                    CASE 
                        WHEN pa.tipo_comparticion = 'usuario' THEN u.nombre
                        WHEN pa.tipo_comparticion = 'grupo' THEN CONCAT(g.nombre, ' (', COUNT(DISTINCT gm.usuario_id), ' miembros)')
                        ELSE NULL
                    END as shared_with_display,
                    pa.usuario_id as shared_with_user_id,
                    pa.grupo_id as shared_with_group_id
                FROM permisos_archivos pa
                JOIN archivos a ON pa.archivo_id = a.id
                LEFT JOIN usuarios u ON pa.usuario_id = u.id AND pa.tipo_comparticion = 'usuario'
                LEFT JOIN grupos g ON pa.grupo_id = g.id AND pa.tipo_comparticion = 'grupo'
                LEFT JOIN grupo_miembros gm ON g.id = gm.grupo_id
                WHERE a.propietario_id = ? AND pa.activo = 1
                GROUP BY pa.id
                
                UNION ALL
                
                SELECT 
                    pc.id as share_id,
                    'carpeta' as resource_type,
                    c.id as resource_id,
                    c.nombre as resource_name,
                    NULL as extension,
                    NULL as tipo_mime,
                    NULL as tamaño,
                    pc.tipo_comparticion,
                    pc.permiso,
                    pc.fecha_otorgado as shared_date,
                    pc.fecha_expiracion as expiry_date,
                    pc.puede_descargar,
                    pc.puede_imprimir,
                    pc.puede_copiar,
                    pc.notificar_cambios,
                    CASE 
                        WHEN pc.tipo_comparticion = 'usuario' THEN u.email
                        WHEN pc.tipo_comparticion = 'grupo' THEN g.nombre
                        ELSE NULL
                    END as shared_with_name,
                    CASE 
                        WHEN pc.tipo_comparticion = 'usuario' THEN u.nombre
                        WHEN pc.tipo_comparticion = 'grupo' THEN CONCAT(g.nombre, ' (', COUNT(DISTINCT gm.usuario_id), ' miembros)')
                        ELSE NULL
                    END as shared_with_display,
                    pc.usuario_id as shared_with_user_id,
                    pc.grupo_id as shared_with_group_id
                FROM permisos_carpetas pc
                JOIN carpetas c ON pc.carpeta_id = c.id
                LEFT JOIN usuarios u ON pc.usuario_id = u.id AND pc.tipo_comparticion = 'usuario'
                LEFT JOIN grupos g ON pc.grupo_id = g.id AND pc.tipo_comparticion = 'grupo'
                LEFT JOIN grupo_miembros gm ON g.id = gm.grupo_id
                WHERE c.propietario_id = ? AND pc.activo = 1
                GROUP BY pc.id
                
                ORDER BY shared_date DESC
            ");
            
            $stmtFiles->execute([$userId, $userId]);
            $shares = $stmtFiles->fetchAll(\PDO::FETCH_ASSOC);
            
            return Response::json([
                'success' => true,
                'shares' => $shares
            ]);
            
        } catch (\Exception $e) {
            error_log("Error obteniendo recursos compartidos: " . $e->getMessage());
            return Response::json(['error' => 'Error al obtener recursos compartidos'], 500);
        }
    }

    /**
     * Actualizar permisos de un recurso compartido
     */
    public function updateSharing()
    {
        $userId = (int)Session::get('user_id');
        $shareId = (int)($_POST['share_id'] ?? 0);
        $resourceType = $_POST['resource_type'] ?? '';
        $permission = $_POST['permission'] ?? '';
        $expiryDate = $_POST['expiry_date'] ?? null;
        $canDownload = isset($_POST['can_download']) ? (int)$_POST['can_download'] : null;
        $canPrint = isset($_POST['can_print']) ? (int)$_POST['can_print'] : null;
        $canCopy = isset($_POST['can_copy']) ? (int)$_POST['can_copy'] : null;

        if (!$shareId || !in_array($resourceType, ['archivo', 'carpeta'])) {
            return Response::json(['error' => 'Datos inválidos'], 422);
        }

        try {
            $table = $resourceType === 'archivo' ? 'permisos_archivos' : 'permisos_carpetas';
            $resourceColumn = $resourceType === 'archivo' ? 'archivo_id' : 'carpeta_id';
            $resourceTable = $resourceType === 'archivo' ? 'archivos' : 'carpetas';
            
            // Verificar que el usuario es el propietario del recurso
            $stmt = Database::connection()->prepare("
                SELECT r.propietario_id 
                FROM {$table} p
                JOIN {$resourceTable} r ON p.{$resourceColumn} = r.id
                WHERE p.id = ?
            ");
            $stmt->execute([$shareId]);
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$data || $data['propietario_id'] != $userId) {
                return Response::json(['error' => 'No tienes permisos para modificar esta compartición'], 403);
            }

            // Construir la consulta de actualización dinámicamente
            $updates = [];
            $params = [];
            
            if ($permission) {
                $updates[] = "permiso = ?";
                $params[] = $permission;
            }
            
            if ($expiryDate !== null) {
                if ($expiryDate === '') {
                    $updates[] = "fecha_expiracion = NULL";
                } else {
                    $updates[] = "fecha_expiracion = ?";
                    $params[] = $expiryDate;
                }
            }
            
            if ($canDownload !== null) {
                $updates[] = "puede_descargar = ?";
                $params[] = $canDownload;
            }
            
            if ($canPrint !== null) {
                $updates[] = "puede_imprimir = ?";
                $params[] = $canPrint;
            }
            
            if ($canCopy !== null) {
                $updates[] = "puede_copiar = ?";
                $params[] = $canCopy;
            }
            
            if (empty($updates)) {
                return Response::json(['error' => 'No hay cambios para actualizar'], 422);
            }
            
            $params[] = $shareId;
            $sql = "UPDATE {$table} SET " . implode(', ', $updates) . " WHERE id = ?";
            
            $stmt = Database::connection()->prepare($sql);
            $stmt->execute($params);
            
            return Response::json([
                'success' => true,
                'message' => 'Permisos actualizados exitosamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("Error actualizando compartición: " . $e->getMessage());
            return Response::json(['error' => 'Error al actualizar permisos'], 500);
        }
    }

    /**
     * Eliminar/Revocar una compartición específica
     */
    public function removeSharing()
    {
        $userId = (int)Session::get('user_id');
        $shareId = (int)($_POST['share_id'] ?? 0);
        $resourceType = $_POST['resource_type'] ?? '';

        if (!$shareId || !in_array($resourceType, ['archivo', 'carpeta'])) {
            return Response::json(['error' => 'Datos inválidos'], 422);
        }

        try {
            $table = $resourceType === 'archivo' ? 'permisos_archivos' : 'permisos_carpetas';
            $resourceColumn = $resourceType === 'archivo' ? 'archivo_id' : 'carpeta_id';
            $resourceTable = $resourceType === 'archivo' ? 'archivos' : 'carpetas';
            
            // Verificar que el usuario es el propietario del recurso
            $stmt = Database::connection()->prepare("
                SELECT r.propietario_id 
                FROM {$table} p
                JOIN {$resourceTable} r ON p.{$resourceColumn} = r.id
                WHERE p.id = ?
            ");
            $stmt->execute([$shareId]);
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$data || $data['propietario_id'] != $userId) {
                return Response::json(['error' => 'No tienes permisos para eliminar esta compartición'], 403);
            }

            // Marcar como inactivo (soft delete)
            $stmt = Database::connection()->prepare("
                UPDATE {$table} SET activo = 0 WHERE id = ?
            ");
            $stmt->execute([$shareId]);
            
            return Response::json([
                'success' => true,
                'message' => 'Compartición eliminada exitosamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("Error eliminando compartición: " . $e->getMessage());
            return Response::json(['error' => 'Error al eliminar compartición'], 500);
        }
    }

    /**
     * Obtener lista de compartidos por recurso
     */
    public function listByResource()
    {
        $userId = (int)Session::get('user_id');
        $resourceId = (int)($_GET['resource_id'] ?? 0);
        $resourceType = $_GET['resource_type'] ?? '';

        if (!$resourceId || !in_array($resourceType, ['archivo', 'carpeta'])) {
            return Response::json(['error' => 'Parámetros inválidos'], 422);
        }

        try {
            $table = $resourceType === 'archivo' ? 'permisos_archivos' : 'permisos_carpetas';
            $resourceColumn = $resourceType === 'archivo' ? 'archivo_id' : 'carpeta_id';
            $resourceTable = $resourceType === 'archivo' ? 'archivos' : 'carpetas';
            
            // Verificar que el usuario es el propietario del recurso
            $stmt = Database::connection()->prepare("
                SELECT propietario_id FROM {$resourceTable} WHERE id = ?
            ");
            $stmt->execute([$resourceId]);
            $resource = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$resource || $resource['propietario_id'] != $userId) {
                return Response::json(['error' => 'No tienes permisos para ver este recurso'], 403);
            }

            // Obtener todos los compartidos para este recurso
            $stmt = Database::connection()->prepare("
                SELECT 
                    p.id as share_id,
                    p.tipo_comparticion,
                    p.usuario_id,
                    p.grupo_id,
                    p.permiso,
                    p.fecha_expiracion,
                    p.puede_descargar,
                    p.puede_imprimir,
                    p.puede_copiar,
                    CASE 
                        WHEN p.tipo_comparticion = 'usuario' THEN u.nombre
                        WHEN p.tipo_comparticion = 'grupo' THEN g.nombre
                        ELSE NULL
                    END as nombre,
                    CASE 
                        WHEN p.tipo_comparticion = 'usuario' THEN u.email
                        WHEN p.tipo_comparticion = 'grupo' THEN NULL
                        ELSE NULL
                    END as email
                FROM {$table} p
                LEFT JOIN usuarios u ON p.usuario_id = u.id AND p.tipo_comparticion = 'usuario'
                LEFT JOIN grupos g ON p.grupo_id = g.id AND p.tipo_comparticion = 'grupo'
                WHERE p.{$resourceColumn} = ? AND p.activo = 1
                ORDER BY p.fecha_otorgado DESC
            ");
            $stmt->execute([$resourceId]);
            $shares = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return Response::json([
                'success' => true,
                'shares' => $shares
            ]);

        } catch (\Exception $e) {
            error_log("Error obteniendo compartidos por recurso: " . $e->getMessage());
            return Response::json(['error' => 'Error al obtener compartidos'], 500);
        }
    }

    /**
     * Buscar usuarios y grupos disponibles
     */
    public function searchUsersGroups()
    {
        $userId = (int)Session::get('user_id');
        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 2) {
            return Response::json(['success' => true, 'results' => []]);
        }

        try {
            $searchTerm = "%{$query}%";
            $results = [];

            // Buscar usuarios
            $stmt = Database::connection()->prepare("
                SELECT id, nombre as name, email, 'user' as type, NULL as member_count
                FROM usuarios 
                WHERE (nombre LIKE ? OR email LIKE ?) AND activo = 1 AND id != ?
                ORDER BY nombre
                LIMIT 10
            ");
            $stmt->execute([$searchTerm, $searchTerm, $userId]);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $results = array_merge($results, $users);

            // Buscar grupos
            $stmt = Database::connection()->prepare("
                SELECT g.id, g.nombre as name, NULL as email, 'group' as type,
                       COUNT(gm.usuario_id) as member_count
                FROM grupos g
                LEFT JOIN grupo_miembros gm ON g.id = gm.grupo_id
                WHERE g.nombre LIKE ? AND g.activo = 1
                GROUP BY g.id
                ORDER BY g.nombre
                LIMIT 10
            ");
            $stmt->execute([$searchTerm]);
            $groups = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $results = array_merge($results, $groups);

            return Response::json([
                'success' => true,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            error_log("Error buscando usuarios/grupos: " . $e->getMessage());
            return Response::json(['error' => 'Error en la búsqueda'], 500);
        }
    }

    /**
     * Agregar nuevo compartido a un recurso
     */
    public function addToResource()
    {
        $userId = (int)Session::get('user_id');
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        $resourceType = $_POST['resource_type'] ?? '';
        $targetType = $_POST['target_type'] ?? '';
        $targetId = (int)($_POST['target_id'] ?? 0);
        $permission = $_POST['permission'] ?? 'lector';
        $expiryDate = $_POST['expiry_date'] ?? '';
        $canDownload = (int)($_POST['can_download'] ?? 0);
        $canPrint = (int)($_POST['can_print'] ?? 0);
        $canCopy = (int)($_POST['can_copy'] ?? 0);

        if (!$resourceId || !in_array($resourceType, ['archivo', 'carpeta']) || 
            !in_array($targetType, ['usuario', 'grupo']) || !$targetId) {
            return Response::json(['error' => 'Datos inválidos'], 422);
        }

        try {
            $table = $resourceType === 'archivo' ? 'permisos_archivos' : 'permisos_carpetas';
            $resourceColumn = $resourceType === 'archivo' ? 'archivo_id' : 'carpeta_id';
            $resourceTable = $resourceType === 'archivo' ? 'archivos' : 'carpetas';
            
            // Verificar que el usuario es el propietario del recurso
            $stmt = Database::connection()->prepare("
                SELECT propietario_id FROM {$resourceTable} WHERE id = ?
            ");
            $stmt->execute([$resourceId]);
            $resource = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$resource || $resource['propietario_id'] != $userId) {
                return Response::json(['error' => 'No tienes permisos para compartir este recurso'], 403);
            }

            // Verificar que no existe ya un compartido para este usuario/grupo
            $targetColumn = $targetType === 'usuario' ? 'usuario_id' : 'grupo_id';
            $stmt = Database::connection()->prepare("
                SELECT id FROM {$table} 
                WHERE {$resourceColumn} = ? AND {$targetColumn} = ? AND tipo_comparticion = ? AND activo = 1
            ");
            $stmt->execute([$resourceId, $targetId, $targetType]);
            
            if ($stmt->fetch()) {
                return Response::json(['error' => 'Este usuario/grupo ya tiene acceso al recurso'], 422);
            }

            // Insertar nuevo compartido
            $stmt = Database::connection()->prepare("
                INSERT INTO {$table} (
                    {$resourceColumn}, tipo_comparticion, {$targetColumn}, permiso, 
                    fecha_expiracion, puede_descargar, puede_imprimir, puede_copiar, 
                    fecha_otorgado, activo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)
            ");
            
            $params = [
                $resourceId, 
                $targetType, 
                $targetId, 
                $permission,
                $expiryDate ?: null,
                $canDownload,
                $canPrint,
                $canCopy
            ];
            
            $stmt->execute($params);

            return Response::json([
                'success' => true,
                'message' => 'Acceso agregado exitosamente'
            ]);

        } catch (\Exception $e) {
            error_log("Error agregando compartido: " . $e->getMessage());
            return Response::json(['error' => 'Error al agregar acceso'], 500);
        }
    }

}
