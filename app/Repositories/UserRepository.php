<?php

namespace App\Repositories;

use App\Services\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email AND activo = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function findByEmailAndPasswordPlain(string $email, string $password): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email AND password = :pwd AND activo = 1 LIMIT 1');
        $stmt->execute(['email' => $email, 'pwd' => $password]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function setPlainPassword(int $id, string $password): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET password = :pwd WHERE id = :id');
        $stmt->execute(['pwd' => $password, 'id' => $id]);
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare('
            SELECT * 
            FROM usuarios 
            ORDER BY nombre ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        return array_map([$this, 'map'], $rows);
    }

    /**
     * Crear un nuevo usuario
     */
    public function createUser(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO usuarios (email, nombre, apellidos, password, rol, departamento, cuota_almacenamiento, activo) 
            VALUES (:email, :nombre, :apellidos, :password, :rol, :departamento, :cuota_almacenamiento, :activo)
        ');
        
        $stmt->execute([
            'email' => $data['email'],
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? null,
            'password' => $data['password'],
            'rol' => $data['rol'],
            'departamento' => $data['departamento'] ?? null,
            'cuota_almacenamiento' => $data['cuota_almacenamiento'] ?? 1073741824,
            'activo' => $data['activo'] ?? 1
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar un usuario existente
     */
    public function updateUser(int $id, array $data): void
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = 'UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Eliminar un usuario
     */
    public function deleteUser(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Buscar usuarios por criterios
     */
    public function searchUsers(string $search = '', string $rol = '', string $departamento = ''): array
    {
        $conditions = ['activo = 1'];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = '(nombre LIKE :search OR apellidos LIKE :search OR email LIKE :search)';
            $params['search'] = "%{$search}%";
        }
        
        if (!empty($rol)) {
            $conditions[] = 'rol = :rol';
            $params['rol'] = $rol;
        }
        
        if (!empty($departamento)) {
            $conditions[] = 'departamento = :departamento';
            $params['departamento'] = $departamento;
        }
        
        $sql = 'SELECT *, fecha_creacion, fecha_ultimo_acceso FROM usuarios WHERE ' . implode(' AND ', $conditions) . ' ORDER BY nombre ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        
        return array_map([$this, 'map'], $rows);
    }

    private function map(array $row): User
    {
        $u = new User();
        $u->id = (int)$row['id'];
        $u->email = $row['email'];
        $u->nombre = $row['nombre'];
        $u->apellidos = $row['apellidos'] ?? null;
        $u->avatar = $row['avatar'] ?? null;
        $u->password = $row['password'] ?? null;
        $u->rol = $row['rol'];
        $u->departamento = $row['departamento'] ?? null;
        $u->activo = (int)$row['activo'];
        $u->cuota_almacenamiento = (int)($row['cuota_almacenamiento'] ?? 1073741824);
        $u->almacenamiento_usado = (int)($row['almacenamiento_usado'] ?? 0);
        
        // Agregar campos de fechas si están disponibles
        if (isset($row['fecha_creacion'])) {
            $u->fecha_creacion = $row['fecha_creacion'];
        }
        if (isset($row['fecha_ultimo_acceso'])) {
            $u->fecha_ultimo_acceso = $row['fecha_ultimo_acceso'];
        }
        
        return $u;
    }
}


