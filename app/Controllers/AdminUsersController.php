<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Models\User;
use App\Services\Session;
use App\Helpers\Response;
use App\Services\Database;

class AdminUsersController
{
    private UserRepository $userRepository;
    private Database $db;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->db = new Database();
    }

    /**
     * Mostrar la página de gestión de usuarios
     */
    public function index()
    {
        // Verificar que el usuario sea administrador
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }

        $users = $this->userRepository->getAllUsers();
        $roles = User::SYSTEM_ROLES;

        Response::view('admin/users', [
            'users' => $users,
            'roles' => $roles,
            'csrf' => $_SESSION['csrf_token'] ?? ''
        ]);
    }

    /**
     * Obtener lista de usuarios vía API
     */
    public function getUsers()
    {
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }

        $users = $this->userRepository->getAllUsers();
        
        // Recalcular uso real para cada usuario
        $this->recalcularUsoAlmacenamiento($users);
        
        // Formatear datos para la respuesta
        $formattedUsers = array_map(function($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'nombre' => $user->nombre,
                'apellidos' => $user->apellidos,
                'rol' => $user->rol,
                'rol_nombre' => $user->getSystemRoleName(),
                'departamento' => $user->departamento,
                'activo' => $user->activo,
                'fecha_creacion' => $user->fecha_creacion ?? null,
                'fecha_ultimo_acceso' => $user->fecha_ultimo_acceso ?? null,
                'cuota_almacenamiento' => $user->cuota_almacenamiento ?? 1073741824, // 1GB default
                'almacenamiento_usado' => $user->almacenamiento_usado ?? 0,
                'cuota_formateada' => $user->getCuotaFormateada(),
                'uso_formateado' => $user->getUsoFormateado(),
                'porcentaje_uso' => $user->getPorcentajeUso()
            ];
        }, $users);

        return Response::json(['users' => $formattedUsers]);
    }

    /**
     * Crear un nuevo usuario
     */
    public function createUser()
    {
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }



        $email = trim($_POST['email'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $rol = $_POST['rol'] ?? 'viewer';
        $departamento = trim($_POST['departamento'] ?? '');
        $password = $_POST['password'] ?? '';
        $cuotaAlmacenamiento = $this->procesarCuotaAlmacenamiento($_POST['cuota_almacenamiento'] ?? '1', $_POST['cuota_unit'] ?? 'GB');

        // Validaciones
        if (empty($email) || empty($nombre) || empty($password)) {
            return Response::json(['error' => 'Email, nombre y contraseña son requeridos'], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::json(['error' => 'Email inválido'], 422);
        }

        if (!array_key_exists($rol, User::SYSTEM_ROLES)) {
            return Response::json(['error' => 'Rol inválido'], 422);
        }

        if (strlen($password) < 6) {
            return Response::json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 422);
        }

        // Verificar que el email no exista
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            return Response::json(['error' => 'El email ya está registrado'], 422);
        }

        try {
            $userId = $this->userRepository->createUser([
                'email' => $email,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'password' => password_hash($password, PASSWORD_ARGON2ID),
                'rol' => $rol,
                'departamento' => $departamento,
                'cuota_almacenamiento' => $cuotaAlmacenamiento,
                'activo' => 1
            ]);

            // Registrar auditoría
            $this->logRoleChange(null, $userId, null, $rol, 'Usuario creado');

            return Response::json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            error_log("Error creando usuario: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Actualizar un usuario existente
     */
    public function updateUser()
    {
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }



        $userId = (int)($_POST['user_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $rol = $_POST['rol'] ?? '';
        $departamento = trim($_POST['departamento'] ?? '');
        $activo = (int)($_POST['activo'] ?? 1);
        $cuotaAlmacenamiento = $this->procesarCuotaAlmacenamiento($_POST['cuota_almacenamiento'] ?? '1', $_POST['cuota_unit'] ?? 'GB');

        if (!$userId) {
            return Response::json(['error' => 'ID de usuario requerido'], 422);
        }

        // Obtener usuario actual
        $currentUser = $this->userRepository->findById($userId);
        if (!$currentUser) {
            return Response::json(['error' => 'Usuario no encontrado'], 404);
        }

        // Validaciones
        if (empty($email) || empty($nombre)) {
            return Response::json(['error' => 'Email y nombre son requeridos'], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::json(['error' => 'Email inválido'], 422);
        }

        if (!array_key_exists($rol, User::SYSTEM_ROLES)) {
            return Response::json(['error' => 'Rol inválido'], 422);
        }

        // Verificar que el email no esté en uso por otro usuario
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser && $existingUser->id !== $userId) {
            return Response::json(['error' => 'El email ya está registrado por otro usuario'], 422);
        }

        try {
            // Debug: Log de los datos recibidos
            error_log("DEBUG updateUser - cuota_almacenamiento: " . ($_POST['cuota_almacenamiento'] ?? 'NO SET'));
            error_log("DEBUG updateUser - cuota_unit: " . ($_POST['cuota_unit'] ?? 'NO SET'));
            error_log("DEBUG updateUser - cuotaAlmacenamiento procesada: " . $cuotaAlmacenamiento);
            
            $updateData = [
                'email' => $email,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'rol' => $rol,
                'departamento' => $departamento,
                'activo' => $activo,
                'cuota_almacenamiento' => $cuotaAlmacenamiento
            ];

            // Si se proporciona nueva contraseña
            if (!empty($_POST['password'])) {
                $password = $_POST['password'];
                if (strlen($password) < 6) {
                    return Response::json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 422);
                }
                $updateData['password'] = password_hash($password, PASSWORD_ARGON2ID);
            }

            $this->userRepository->updateUser($userId, $updateData);

            // Registrar auditoría si cambió el rol
            if ($currentUser->rol !== $rol) {
                $this->logRoleChange($userId, Session::get('user_id'), $currentUser->rol, $rol, 'Rol actualizado por administrador');
            }

            return Response::json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            error_log("Error actualizando usuario: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Eliminar un usuario
     */
    public function deleteUser()
    {
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }



        $userId = (int)($_POST['user_id'] ?? 0);

        if (!$userId) {
            return Response::json(['error' => 'ID de usuario requerido'], 422);
        }

        // No permitir eliminar al propio usuario
        if ($userId === Session::get('user_id')) {
            return Response::json(['error' => 'No puedes eliminarte a ti mismo'], 422);
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return Response::json(['error' => 'Usuario no encontrado'], 404);
        }

        try {
            $this->userRepository->deleteUser($userId);

            return Response::json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            error_log("Error eliminando usuario: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Cambiar el estado activo/inactivo de un usuario
     */
    public function toggleUserStatus()
    {
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }



        $userId = (int)($_POST['user_id'] ?? 0);

        if (!$userId) {
            return Response::json(['error' => 'ID de usuario requerido'], 422);
        }

        // No permitir desactivar al propio usuario
        if ($userId === Session::get('user_id')) {
            return Response::json(['error' => 'No puedes desactivarte a ti mismo'], 422);
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return Response::json(['error' => 'Usuario no encontrado'], 404);
        }

        try {
            $newStatus = $user->activo ? 0 : 1;
            $this->userRepository->updateUser($userId, ['activo' => $newStatus]);

            $statusText = $newStatus ? 'activado' : 'desactivado';

            return Response::json([
                'success' => true,
                'message' => "Usuario {$statusText} exitosamente",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            error_log("Error cambiando estado de usuario: " . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Verificar si el usuario actual es administrador
     */
    private function isAdmin(): bool
    {
        $userRole = Session::get('user_role');
        return $userRole === 'administrador';
    }

    /**
     * Recalcular el uso real de almacenamiento para cada usuario
     */
    private function recalcularUsoAlmacenamiento(array $users): void
    {
        $db = Database::connection();
        
        foreach ($users as $user) {
            // Calcular uso real sumando tamaños de archivos
            $stmt = $db->prepare('
                SELECT COALESCE(SUM(tamaño), 0) as uso_real 
                FROM archivos 
                WHERE propietario_id = ? AND tamaño IS NOT NULL
            ');
            $stmt->execute([$user->id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $usoReal = (int)$result['uso_real'];
            
            // Actualizar si hay diferencia
            if ($user->almacenamiento_usado !== $usoReal) {
                $updateStmt = $db->prepare('
                    UPDATE usuarios 
                    SET almacenamiento_usado = ? 
                    WHERE id = ?
                ');
                $updateStmt->execute([$usoReal, $user->id]);
                
                // Actualizar objeto en memoria
                $user->almacenamiento_usado = $usoReal;
            }
        }
    }

    /**
     * Registrar cambio de rol en auditoría
     */
    private function logRoleChange(?int $userId, int $changedBy, ?string $oldRole, string $newRole, string $reason = '')
    {
        try {
            $stmt = Database::connection()->prepare("
                INSERT INTO auditoria_roles (usuario_id, rol_anterior, rol_nuevo, cambiado_por, motivo) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $oldRole, $newRole, $changedBy, $reason]);
        } catch (\Exception $e) {
            error_log("Error registrando auditoría de rol: " . $e->getMessage());
        }
    }

    /**
     * Buscar usuarios para autocompletado
     */
    public function searchUsers()
    {
        if (!$this->isAdmin()) {
            return Response::json(['error' => 'Acceso denegado'], 403);
        }

        $query = $_GET['q'] ?? '';
        $query = trim($query);

        if (strlen($query) < 2) {
            return Response::json(['users' => []]);
        }

        try {
            $stmt = Database::connection()->prepare("
                SELECT id, nombre, email 
                FROM usuarios 
                WHERE activo = 1 
                AND (nombre LIKE ? OR email LIKE ?)
                ORDER BY nombre
                LIMIT 20
            ");
            
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm]);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return Response::json([
                'success' => true,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            error_log('Error searching users: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'error' => 'Error al buscar usuarios'
            ], 500);
        }
    }

    /**
     * Procesar cuota de almacenamiento desde el formulario
     */
    private function procesarCuotaAlmacenamiento(string $valor, string $unidad): int
    {
        $multipliers = [
            'MB' => 1048576,      // 1024^2
            'GB' => 1073741824,   // 1024^3
            'TB' => 1099511627776 // 1024^4
        ];
        
        $valorNumerico = floatval($valor);
        $multiplicador = $multipliers[$unidad] ?? $multipliers['GB'];
        
        // Asegurar un mínimo de 1MB
        return max(1048576, (int)($valorNumerico * $multiplicador));
    }
}
