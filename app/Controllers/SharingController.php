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
        $userId = Session::get('user_id');
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
                $stmt = Database::connection()->prepare("
                    SELECT id FROM permisos_recursos 
                    WHERE recurso_tipo = ? AND recurso_id = ? AND usuario_id = ? AND activo = 1
                ");
                $stmt->execute([$resourceType, $resourceId, $targetUser->id]);
                $existingPermission = $stmt->fetch();

                if ($existingPermission) {
                    // Actualizar permiso existente
                    $stmt = Database::connection()->prepare("
                        UPDATE permisos_recursos 
                        SET permiso = ?, fecha_expiracion = ?, puede_descargar = ?, 
                            puede_imprimir = ?, puede_copiar = ?, notificar_cambios = ?, 
                            mensaje = ?, fecha_actualizacion = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $permission, $expiryDate, $canDownload, $canPrint, 
                        $canCopy, $notifyChanges, $message, $existingPermission['id']
                    ]);
                } else {
                    // Crear nuevo permiso
                    $stmt = Database::connection()->prepare("
                        INSERT INTO permisos_recursos 
                        (recurso_tipo, recurso_id, tipo_comparticion, usuario_id, permiso, fecha_expiracion, 
                         puede_descargar, puede_imprimir, puede_copiar, notificar_cambios, mensaje,
                         otorgado_por, activo, fecha_creacion)
                        VALUES (?, ?, 'usuario', ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
                    ");
                    $stmt->execute([
                        $resourceType, $resourceId, $targetUser->id, $permission,
                        $expiryDate, $canDownload, $canPrint, $canCopy, 
                        $notifyChanges, $message, $userId
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
        $userId = Session::get('user_id');
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

            // Crear permiso para el grupo
            $stmt = Database::connection()->prepare("
                INSERT INTO permisos_recursos 
                (recurso_tipo, recurso_id, tipo_comparticion, grupo_id, permiso, fecha_expiracion, 
                 puede_descargar, puede_imprimir, puede_copiar, notificar_cambios, 
                 otorgado_por, activo, fecha_creacion)
                VALUES (?, ?, 'grupo', ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE 
                permiso = VALUES(permiso), fecha_expiracion = VALUES(fecha_expiracion),
                puede_descargar = VALUES(puede_descargar), puede_imprimir = VALUES(puede_imprimir),
                puede_copiar = VALUES(puede_copiar), notificar_cambios = VALUES(notificar_cambios),
                fecha_actualizacion = NOW()
            ");
            
            $stmt->execute([
                $resourceType, $resourceId, $groupId, $permission, $expiryDate,
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
        $userId = Session::get('user_id');
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
            $passwordHash = $password ? password_hash($password, PASSWORD_ARGON2ID) : null;
            
            // Generar código de acceso si se solicita
            $accessCode = $useAccessCode ? $this->generateAccessCode() : null;

            // Crear enlace compartido
            $stmt = Database::connection()->prepare("
                INSERT INTO enlaces_compartidos 
                (token, tipo, recurso_id, creado_por, rol_acceso, fecha_expiracion, 
                 requiere_autenticacion, dominios_permitidos, puede_descargar, 
                 puede_imprimir, puede_copiar, notificar_accesos, requiere_password, password_hash, contraseña, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");

            $stmt->execute([
                $token,
                $resourceType,
                $resourceId,
                $userId,
                $permission,
                $expiryDate,
                $requiresAuth,
                $allowedDomains,
                $canDownload,
                $canPrint,
                $canCopy,
                $notifyAccess,
                ($password || $accessCode) ? 1 : 0,
                $passwordHash,
                $accessCode
            ]);

            $linkId = Database::connection()->lastInsertId();
            $publicUrl = "http://localhost:8888/biblioteca/public/index.php/s/$token";

            return Response::json([
                'success' => true,
                'message' => 'Enlace público creado exitosamente',
                'link_id' => $linkId,
                'token' => $token,
                'url' => $publicUrl,
                'permission' => User::getSharingRoleName($permission),
                'expires' => $expiryDate,
                'requires_password' => (bool)$password,
                'access_code' => $accessCode,
                'requires_access_code' => (bool)$accessCode
            ]);

        } catch (\Exception $e) {
            error_log("Error creando enlace público: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener permisos de un recurso
     */
    public function getResourcePermissions()
    {
        $resourceType = $_GET['resource_type'] ?? '';
        $resourceId = (int)($_GET['resource_id'] ?? 0);
        $userId = Session::get('user_id');

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
        $userId = Session::get('user_id');

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
        $userId = Session::get('user_id');

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
            $table = $resourceType === 'archivo' ? 'archivos' : 'carpetas';
            $stmt = Database::connection()->prepare("
                SELECT propietario_id FROM {$table} 
                WHERE id = ? AND propietario_id = ? AND activo = 1
            ");
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
        $userId = Session::get('user_id');
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

}
