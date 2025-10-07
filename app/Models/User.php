<?php

namespace App\Models;

class User
{
    public int $id;
    public string $email;
    public string $nombre;
    public ?string $apellidos = null;
    public ?string $avatar = null;
    public ?string $password = null;
    public string $rol = 'usuario_editor';
    public int $cuota_almacenamiento = 1073741824; // 1GB por defecto
    public int $almacenamiento_usado = 0;
    
    // ROLES DE SISTEMA (para usuarios)
    public const SYSTEM_ROLES = [
        'administrador' => 'Administrador',
        'usuario_editor' => 'Usuario Editor'
    ];
    
    // ROLES DE COMPARTICIÓN (para archivos/carpetas)
    public const SHARING_ROLES = [
        'propietario' => 'Propietario',
        'editor' => 'Editor', 
        'comentarista' => 'Comentarista',
        'lector' => 'Lector/Visualizador'
    ];
    
    // PERMISOS POR ROL DE SISTEMA
    public const SYSTEM_PERMISSIONS = [
        'administrador' => [
            'sistema' => ['crear', 'leer', 'actualizar', 'eliminar', 'administrar'],
            'usuarios' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'archivos' => ['ver_todos', 'crear', 'leer', 'actualizar', 'eliminar', 'compartir', 'transferir'],
            'carpetas' => ['ver_todas', 'crear', 'leer', 'actualizar', 'eliminar', 'compartir', 'administrar']
        ],
        'usuario_editor' => [
            'archivos' => ['crear', 'leer_propios', 'actualizar_propios', 'eliminar_propios', 'compartir_propios'],
            'carpetas' => ['crear', 'leer_propias', 'actualizar_propias', 'eliminar_propias', 'compartir_propias']
        ]
    ];
    
    // PERMISOS POR ROL DE COMPARTICIÓN
    public const SHARING_PERMISSIONS = [
        'propietario' => [
            'recurso' => ['leer', 'actualizar', 'eliminar', 'comentar', 'compartir', 'transferir_propiedad', 'gestionar_permisos', 'descargar', 'imprimir', 'copiar']
        ],
        'editor' => [
            'recurso' => ['leer', 'actualizar', 'comentar', 'descargar', 'imprimir', 'copiar']
        ],
        'comentarista' => [
            'recurso' => ['leer', 'comentar', 'descargar']
        ],
        'lector' => [
            'recurso' => ['leer']
        ]
    ];
    
    /**
     * Verifica si el usuario tiene un permiso específico del sistema
     */
    public function hasSystemPermission(string $resource, string $action): bool
    {
        $permissions = self::SYSTEM_PERMISSIONS[$this->rol] ?? [];
        return in_array($action, $permissions[$resource] ?? []);
    }
    
    /**
     * Verifica si un rol de compartición tiene un permiso específico
     */
    public static function hasSharingPermission(string $sharingRole, string $action): bool
    {
        $permissions = self::SHARING_PERMISSIONS[$sharingRole] ?? [];
        return in_array($action, $permissions['recurso'] ?? []);
    }
    
    /**
     * Verifica si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->rol === 'administrador';
    }
    
    /**
     * Verifica si el usuario puede gestionar otros usuarios
     */
    public function canManageUsers(): bool
    {
        return $this->hasSystemPermission('usuarios', 'crear') || $this->hasSystemPermission('usuarios', 'actualizar');
    }
    
    /**
     * Obtiene el nombre legible del rol de sistema
     */
    public function getSystemRoleName(): string
    {
        return self::SYSTEM_ROLES[$this->rol] ?? 'Desconocido';
    }
    
    /**
     * Obtiene el nombre legible de un rol de compartición
     */
    public static function getSharingRoleName(string $sharingRole): string
    {
        return self::SHARING_ROLES[$sharingRole] ?? 'Desconocido';
    }
    
    /**
     * Verifica si el usuario puede ver todos los archivos (solo admin)
     */
    public function canViewAllFiles(): bool
    {
        return $this->hasSystemPermission('archivos', 'ver_todos');
    }
    
    /**
     * Verifica si el usuario puede ver todas las carpetas (solo admin)
     */
    public function canViewAllFolders(): bool
    {
        return $this->hasSystemPermission('carpetas', 'ver_todas');
    }

    /**
     * Obtener cuota formateada en MB
     */
    public function getCuotaFormateada(): string
    {
        return round($this->cuota_almacenamiento / 1024 / 1024, 2) . ' MB';
    }

    /**
     * Obtener uso formateado en MB
     */
    public function getUsoFormateado(): string
    {
        return round($this->almacenamiento_usado / 1024 / 1024, 2) . ' MB';
    }

    /**
     * Obtener porcentaje de uso
     */
    public function getPorcentajeUso(): float
    {
        if ($this->cuota_almacenamiento == 0) {
            return 0;
        }
        return round(($this->almacenamiento_usado / $this->cuota_almacenamiento) * 100, 2);
    }

    /**
     * Obtener espacio disponible en bytes
     */
    public function getEspacioDisponible(): int
    {
        return max(0, $this->cuota_almacenamiento - $this->almacenamiento_usado);
    }

    /**
     * Verificar si tiene espacio suficiente
     */
    public function tieneEspacioSuficiente(int $tamanoBytesNecesarios): bool
    {
        return $this->getEspacioDisponible() >= $tamanoBytesNecesarios;
    }

    /**
     * Verificar si está cerca del límite (>90%)
     */
    public function estaCercaDelLimite(): bool
    {
        return $this->getPorcentajeUso() >= 90;
    }

    /**
     * Verificar si excedió el límite
     */
    public function excedioLimite(): bool
    {
        return $this->almacenamiento_usado > $this->cuota_almacenamiento;
    }

    public ?string $departamento = null;
    public int $activo = 1;
    public ?string $fecha_creacion = null;
    public ?string $fecha_ultimo_acceso = null;
}


