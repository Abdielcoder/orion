<?php

namespace App\Repositories;

use App\Services\Database;
use App\Models\FileItem;
use PDO;

class FileRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function listByFolder(?int $folderId, int $ownerId): array
    {
        // Filtrar archivos por propietario para aislamiento de usuarios
        if ($folderId === 0 || $folderId === null) {
            // Para la raíz, buscar archivos con carpeta_id NULL
            $stmt = $this->db->prepare('SELECT * FROM archivos WHERE carpeta_id IS NULL AND propietario_id = :owner AND activo = 1 ORDER BY nombre');
            $stmt->execute(['owner' => $ownerId]);
        } else {
            $stmt = $this->db->prepare('SELECT * FROM archivos WHERE carpeta_id = :fid AND propietario_id = :owner AND activo = 1 ORDER BY nombre');
            $stmt->execute(['fid' => $folderId, 'owner' => $ownerId]);
        }
        
        $out = [];
        while ($row = $stmt->fetch()) {
            $out[] = $this->map($row);
        }
        return $out;
    }

    public function create(FileItem $f): int
    {
        $stmt = $this->db->prepare('INSERT INTO archivos (nombre, nombre_original, tipo_mime, extension, tamaño, carpeta_id, propietario_id, version, ruta_local, activo) VALUES (:nombre,:nombre_original,:tipo_mime,:extension,:tamano,:carpeta_id,:propietario_id,:version,:ruta_local,:activo)');
        $stmt->execute([
            'nombre' => $f->nombre,
            'nombre_original' => $f->nombre_original,
            'tipo_mime' => $f->tipo_mime,
            'extension' => $f->extension,
            'tamano' => $f->tamaño,
            'carpeta_id' => $f->carpeta_id,
            'propietario_id' => $f->propietario_id,
            'version' => $f->version,
            'ruta_local' => $f->ruta_local,
            'activo' => $f->activo,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?FileItem
    {
        $stmt = $this->db->prepare('SELECT * FROM archivos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function findByIdAndOwner(int $id, int $ownerId): ?FileItem
    {
        $stmt = $this->db->prepare('SELECT * FROM archivos WHERE id = :id AND propietario_id = :owner LIMIT 1');
        $stmt->execute(['id' => $id, 'owner' => $ownerId]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function rename(int $id, string $newName, int $ownerId): void
    {
        $stmt = $this->db->prepare('UPDATE archivos SET nombre = :name WHERE id = :id AND propietario_id = :owner');
        $stmt->execute(['name' => $newName, 'id' => $id, 'owner' => $ownerId]);
    }

    public function move(int $id, ?int $newFolderId, int $ownerId): void
    {
        $stmt = $this->db->prepare('UPDATE archivos SET carpeta_id = :fid WHERE id = :id AND propietario_id = :owner');
        $stmt->execute(['fid' => $newFolderId, 'id' => $id, 'owner' => $ownerId]);
    }

    public function delete(int $id, int $ownerId): void
    {
        $stmt = $this->db->prepare('UPDATE archivos SET activo = 0 WHERE id = :id AND propietario_id = :owner');
        $stmt->execute(['id' => $id, 'owner' => $ownerId]);
    }

    private function map(array $row): FileItem
    {
        $f = new FileItem();
        $f->id = (int)$row['id'];
        $f->nombre = $row['nombre'];
        $f->nombre_original = $row['nombre_original'] ?? null;
        $f->tipo_mime = $row['tipo_mime'] ?? null;
        $f->extension = $row['extension'] ?? null;
        $f->tamaño = $row['tamaño'] !== null ? (int)$row['tamaño'] : null;
        $f->carpeta_id = $row['carpeta_id'] !== null ? (int)$row['carpeta_id'] : null;
        $f->propietario_id = (int)$row['propietario_id'];
        $f->version = (int)$row['version'];
        $f->ruta_local = $row['ruta_local'] ?? '';
        $f->activo = (int)$row['activo'];
        return $f;
    }
}


