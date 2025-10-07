<?php

namespace App\Controllers;

use App\Services\Database;
use App\Services\Session;
use App\Helpers\Response;
use Exception;

class AdminGroupsController
{
    public function __construct()
    {
        // Verificar que el usuario sea administrador
        $userRole = Session::get('user_role');
        if ($userRole !== 'administrador') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
    }

    /**
     * Obtener lista de grupos
     */
    public function getGroups()
    {
        try {
            $stmt = Database::connection()->prepare("
                SELECT g.*, 
                       COUNT(gm.usuario_id) as miembros_count,
                       u.nombre as creado_por_nombre
                FROM grupos g
                LEFT JOIN grupo_miembros gm ON g.id = gm.grupo_id
                LEFT JOIN usuarios u ON g.creado_por = u.id
                WHERE g.activo = 1
                GROUP BY g.id
                ORDER BY g.fecha_creacion DESC
            ");
            $stmt->execute();
            $groups = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return Response::json([
                'success' => true,
                'groups' => $groups
            ]);
        } catch (Exception $e) {
            error_log('Error getting groups: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al obtener grupos'
            ], 500);
        }
    }

    /**
     * Crear nuevo grupo
     */
    public function createGroup()
    {
        $userId = (int)Session::get('user_id');
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $members = json_decode($_POST['members'] ?? '[]', true);

        if (empty($nombre)) {
            return Response::json([
                'success' => false,
                'error' => 'El nombre del grupo es requerido'
            ], 422);
        }

        try {
            Database::connection()->beginTransaction();

            // Crear el grupo
            $stmt = Database::connection()->prepare("
                INSERT INTO grupos (nombre, descripcion, creado_por, fecha_creacion, activo)
                VALUES (?, ?, ?, NOW(), 1)
            ");
            $stmt->execute([$nombre, $descripcion ?: null, $userId]);
            $groupId = Database::connection()->lastInsertId();

            // Agregar miembros al grupo
            if (!empty($members) && is_array($members)) {
                $stmt = Database::connection()->prepare("
                    INSERT INTO grupo_miembros (grupo_id, usuario_id, fecha_agregado)
                    VALUES (?, ?, NOW())
                ");
                
                foreach ($members as $memberId) {
                    if (is_numeric($memberId)) {
                        $stmt->execute([$groupId, (int)$memberId]);
                    }
                }
            }

            Database::connection()->commit();

            return Response::json([
                'success' => true,
                'message' => 'Grupo creado exitosamente',
                'group_id' => $groupId
            ]);

        } catch (Exception $e) {
            Database::connection()->rollBack();
            error_log('Error creating group: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al crear el grupo'
            ], 500);
        }
    }

    /**
     * Actualizar grupo
     */
    public function updateGroup()
    {
        $groupId = (int)($_POST['group_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $members = json_decode($_POST['members'] ?? '[]', true);

        if (!$groupId || empty($nombre)) {
            return Response::json([
                'success' => false,
                'error' => 'ID del grupo y nombre son requeridos'
            ], 422);
        }

        try {
            Database::connection()->beginTransaction();

            // Actualizar información del grupo
            $stmt = Database::connection()->prepare("
                UPDATE grupos 
                SET nombre = ?, descripcion = ?, fecha_modificacion = NOW()
                WHERE id = ? AND activo = 1
            ");
            $stmt->execute([$nombre, $descripcion ?: null, $groupId]);

            // Eliminar miembros actuales
            $stmt = Database::connection()->prepare("
                DELETE FROM grupo_miembros WHERE grupo_id = ?
            ");
            $stmt->execute([$groupId]);

            // Agregar nuevos miembros
            if (!empty($members) && is_array($members)) {
                $stmt = Database::connection()->prepare("
                    INSERT INTO grupo_miembros (grupo_id, usuario_id, fecha_agregado)
                    VALUES (?, ?, NOW())
                ");
                
                foreach ($members as $memberId) {
                    if (is_numeric($memberId)) {
                        $stmt->execute([$groupId, (int)$memberId]);
                    }
                }
            }

            Database::connection()->commit();

            return Response::json([
                'success' => true,
                'message' => 'Grupo actualizado exitosamente'
            ]);

        } catch (Exception $e) {
            Database::connection()->rollBack();
            error_log('Error updating group: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al actualizar el grupo'
            ], 500);
        }
    }

    /**
     * Eliminar grupo
     */
    public function deleteGroup()
    {
        $groupId = (int)($_POST['group_id'] ?? 0);

        if (!$groupId) {
            return Response::json([
                'success' => false,
                'error' => 'ID del grupo es requerido'
            ], 422);
        }

        try {
            Database::connection()->beginTransaction();

            // Marcar grupo como inactivo
            $stmt = Database::connection()->prepare("
                UPDATE grupos 
                SET activo = 0, fecha_modificacion = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$groupId]);

            // Eliminar miembros del grupo
            $stmt = Database::connection()->prepare("
                DELETE FROM grupo_miembros WHERE grupo_id = ?
            ");
            $stmt->execute([$groupId]);

            // Eliminar permisos asociados al grupo
            $stmt = Database::connection()->prepare("
                DELETE FROM permisos_recursos 
                WHERE grupo_id = ?
            ");
            $stmt->execute([$groupId]);

            Database::connection()->commit();

            return Response::json([
                'success' => true,
                'message' => 'Grupo eliminado exitosamente'
            ]);

        } catch (Exception $e) {
            Database::connection()->rollBack();
            error_log('Error deleting group: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al eliminar el grupo'
            ], 500);
        }
    }

    /**
     * Obtener miembros de un grupo
     */
    public function getGroupMembers()
    {
        $groupId = (int)($_GET['group_id'] ?? 0);

        if (!$groupId) {
            return Response::json([
                'success' => false,
                'error' => 'ID del grupo es requerido'
            ], 422);
        }

        try {
            $stmt = Database::connection()->prepare("
                SELECT u.id, u.nombre, u.email, gm.fecha_agregado
                FROM grupo_miembros gm
                JOIN usuarios u ON gm.usuario_id = u.id
                WHERE gm.grupo_id = ? AND u.activo = 1
                ORDER BY u.nombre
            ");
            $stmt->execute([$groupId]);
            $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return Response::json([
                'success' => true,
                'members' => $members
            ]);

        } catch (Exception $e) {
            error_log('Error getting group members: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al obtener miembros del grupo'
            ], 500);
        }
    }

    /**
     * Agregar miembro a grupo
     */
    public function addMember()
    {
        $groupId = (int)($_POST['group_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);

        if (!$groupId || !$userId) {
            return Response::json([
                'success' => false,
                'error' => 'ID del grupo e ID del usuario son requeridos'
            ], 422);
        }

        try {
            // Verificar si ya es miembro
            $stmt = Database::connection()->prepare("
                SELECT COUNT(*) FROM grupo_miembros 
                WHERE grupo_id = ? AND usuario_id = ?
            ");
            $stmt->execute([$groupId, $userId]);
            
            if ($stmt->fetchColumn() > 0) {
                return Response::json([
                    'success' => false,
                    'error' => 'El usuario ya es miembro del grupo'
                ], 422);
            }

            // Agregar miembro
            $stmt = Database::connection()->prepare("
                INSERT INTO grupo_miembros (grupo_id, usuario_id, fecha_agregado)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$groupId, $userId]);

            return Response::json([
                'success' => true,
                'message' => 'Miembro agregado exitosamente'
            ]);

        } catch (Exception $e) {
            error_log('Error adding member: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al agregar miembro'
            ], 500);
        }
    }

    /**
     * Remover miembro de grupo
     */
    public function removeMember()
    {
        $groupId = (int)($_POST['group_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);

        if (!$groupId || !$userId) {
            return Response::json([
                'success' => false,
                'error' => 'ID del grupo e ID del usuario son requeridos'
            ], 422);
        }

        try {
            $stmt = Database::connection()->prepare("
                DELETE FROM grupo_miembros 
                WHERE grupo_id = ? AND usuario_id = ?
            ");
            $stmt->execute([$groupId, $userId]);

            return Response::json([
                'success' => true,
                'message' => 'Miembro removido exitosamente'
            ]);

        } catch (Exception $e) {
            error_log('Error removing member: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al remover miembro'
            ], 500);
        }
    }
}
?>
