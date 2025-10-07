<?php

namespace App\Repositories;

use App\Services\Database;
use App\Models\Folder;
use PDO;

class FolderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function listByParent(?int $parentId, int $ownerId): array
    {
        // Filtrar por propietario para aislamiento de usuarios
        if ($parentId === null || $parentId === 0) {
            // Mostrar carpetas raíz del usuario
            $stmt = $this->db->prepare('SELECT * FROM carpetas WHERE (padre_id IS NULL OR nivel = 0) AND propietario_id = :owner AND activa = 1 ORDER BY nombre');
            $stmt->execute(['owner' => $ownerId]);
        } else {
            // Verificar que la carpeta padre pertenezca al usuario y listar subcarpetas
            $stmt = $this->db->prepare('SELECT * FROM carpetas WHERE padre_id = :pid AND propietario_id = :owner AND activa = 1 ORDER BY nombre');
            $stmt->execute(['pid' => $parentId, 'owner' => $ownerId]);
        }
        $out = [];
        while ($row = $stmt->fetch()) {
            $out[] = $this->map($row);
        }
        return $out;
    }

    public function listAllByParent(?int $parentId): array
    {
        if ($parentId === null) {
            $stmt = $this->db->query('SELECT * FROM carpetas WHERE padre_id IS NULL AND activa = 1 ORDER BY nombre');
        } else {
            $stmt = $this->db->prepare('SELECT * FROM carpetas WHERE padre_id = :pid AND activa = 1 ORDER BY nombre');
            $stmt->execute(['pid' => $parentId]);
        }
        $out = [];
        while ($row = $stmt->fetch()) {
            $out[] = $this->map($row);
        }
        return $out;
    }

    public function create(string $nombre, ?int $parentId, int $ownerId): int
    {
        $stmt = $this->db->prepare('INSERT INTO carpetas (nombre, descripcion, padre_id, nivel, ruta_completa, propietario_id, activa) VALUES (:nombre, NULL, :padre, 0, NULL, :owner, 1)');
        $stmt->execute(['nombre' => $nombre, 'padre' => $parentId, 'owner' => $ownerId]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?Folder
    {
        $stmt = $this->db->prepare('SELECT * FROM carpetas WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function findByIdAndOwner(int $id, int $ownerId): ?Folder
    {
        $stmt = $this->db->prepare('SELECT * FROM carpetas WHERE id = :id AND propietario_id = :owner LIMIT 1');
        $stmt->execute(['id' => $id, 'owner' => $ownerId]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function rename(int $id, string $newName, int $ownerId): void
    {
        $stmt = $this->db->prepare('UPDATE carpetas SET nombre = :name WHERE id = :id AND propietario_id = :owner');
        $stmt->execute(['name' => $newName, 'id' => $id, 'owner' => $ownerId]);
    }

    public function move(int $id, ?int $newParentId, int $ownerId): void
    {
        // Verificar que tanto la carpeta como el destino pertenezcan al usuario
        $stmt = $this->db->prepare('UPDATE carpetas SET padre_id = :parent WHERE id = :id AND propietario_id = :owner');
        $stmt->execute(['parent' => $newParentId, 'id' => $id, 'owner' => $ownerId]);
    }

    public function delete(int $id, int $ownerId): void
    {
        $stmt = $this->db->prepare('UPDATE carpetas SET activa = 0 WHERE id = :id AND propietario_id = :owner');
        $stmt->execute(['id' => $id, 'owner' => $ownerId]);
    }

    public function getBreadcrumb(int $id): array
    {
        $breadcrumb = [];
        $current = $this->findById($id);
        while ($current) {
            array_unshift($breadcrumb, ['id' => $current->id, 'nombre' => $current->nombre]);
            $current = $current->padre_id ? $this->findById($current->padre_id) : null;
        }
        return $breadcrumb;
    }

    public function setLabel(int $id, ?string $etiqueta, ?string $color): void
    {
        $stmt = $this->db->prepare('UPDATE carpetas SET etiqueta = :etiqueta, color_etiqueta = :color WHERE id = :id');
        $stmt->execute(['etiqueta' => $etiqueta, 'color' => $color, 'id' => $id]);
    }

    public function setIcon(int $id, ?string $icono): void
    {
        $stmt = $this->db->prepare('UPDATE carpetas SET icono_personalizado = :icono WHERE id = :id');
        $stmt->execute(['icono' => $icono, 'id' => $id]);
    }

    public function findOrCreateRoot(int $ownerId): Folder
    {
        // Buscar cualquier carpeta del usuario para usar como raíz temporal
        $stmt = $this->db->prepare('SELECT * FROM carpetas WHERE propietario_id = :owner AND activa = 1 ORDER BY id LIMIT 1');
        $stmt->execute(['owner' => $ownerId]);
        $row = $stmt->fetch();
        
        if ($row) {
            return $this->map($row);
        }
        
        // Si no hay carpetas del usuario, crear una carpeta raíz
        $stmt = $this->db->prepare('INSERT INTO carpetas (nombre, descripcion, padre_id, nivel, propietario_id, activa) VALUES ("Mi Drive", "Carpeta raíz del usuario", NULL, 0, :owner, 1)');
        $stmt->execute(['owner' => $ownerId]);
        $id = (int)$this->db->lastInsertId();
        
        return $this->findById($id);
    }

    private function map(array $row): Folder
    {
        $f = new Folder();
        $f->id = (int)$row['id'];
        $f->nombre = $row['nombre'];
        $f->padre_id = $row['padre_id'] !== null ? (int)$row['padre_id'] : null;
        $f->propietario_id = (int)$row['propietario_id'];
        $f->activa = (int)$row['activa'];
        $f->etiqueta = $row['etiqueta'] ?? null;
        $f->color_etiqueta = $row['color_etiqueta'] ?? null;
        $f->icono_personalizado = $row['icono_personalizado'] ?? null;
        return $f;
    }
}


