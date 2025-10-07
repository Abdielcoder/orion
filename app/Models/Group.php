<?php

namespace App\Models;

class Group
{
    public int $id;
    public string $nombre;
    public ?string $descripcion = null;
    public int $creado_por;
    public int $activo = 1;
    public ?string $fecha_creacion = null;
    public ?string $fecha_actualizacion = null;
    
    // Relaciones
    public array $miembros = [];
    
    /**
     * Obtiene el nombre del creador del grupo
     */
    public function getCreatorName(): string
    {
        // Esta función se implementará cuando tengamos el repositorio
        return "Usuario #{$this->creado_por}";
    }
    
    /**
     * Cuenta el número de miembros
     */
    public function getMemberCount(): int
    {
        return count($this->miembros);
    }
    
    /**
     * Verifica si un usuario es miembro del grupo
     */
    public function hasMember(int $userId): bool
    {
        return in_array($userId, array_column($this->miembros, 'usuario_id'));
    }
}
