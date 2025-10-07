<?php

namespace App\Models;

class Permission
{
    public int $id;
    public int $archivo_id;
    public int $usuario_id;
    public string $permiso; // propietario, editor, comentarista, lector
    public int $otorgado_por;
    public ?string $fecha_otorgado = null;
    public ?string $fecha_expiracion = null;
    public int $activo = 1;
    
    // Campos de compartición avanzada
    public string $tipo_comparticion = 'usuario'; // usuario, grupo, enlace
    public ?int $grupo_id = null;
    public ?string $enlace_token = null;
    
    // Restricciones
    public int $puede_descargar = 1;
    public int $puede_imprimir = 1;
    public int $puede_copiar = 1;
    public int $notificar_cambios = 0;
    
    /**
     * Verifica si el permiso ha expirado
     */
    public function isExpired(): bool
    {
        if (!$this->fecha_expiracion) {
            return false;
        }
        
        return strtotime($this->fecha_expiracion) < time();
    }
    
    /**
     * Verifica si el permiso está activo y no ha expirado
     */
    public function isValid(): bool
    {
        return $this->activo && !$this->isExpired();
    }
    
    /**
     * Obtiene el nombre legible del permiso
     */
    public function getPermissionName(): string
    {
        return User::getSharingRoleName($this->permiso);
    }
    
    /**
     * Verifica si tiene un permiso específico
     */
    public function hasAction(string $action): bool
    {
        return User::hasSharingPermission($this->permiso, $action);
    }
    
    /**
     * Obtiene las restricciones como array
     */
    public function getRestrictions(): array
    {
        return [
            'puede_descargar' => (bool)$this->puede_descargar,
            'puede_imprimir' => (bool)$this->puede_imprimir,
            'puede_copiar' => (bool)$this->puede_copiar,
            'notificar_cambios' => (bool)$this->notificar_cambios
        ];
    }
    
    /**
     * Obtiene el tipo de compartición legible
     */
    public function getSharingTypeName(): string
    {
        return match($this->tipo_comparticion) {
            'usuario' => 'Usuario específico',
            'grupo' => 'Grupo',
            'enlace' => 'Enlace público',
            default => 'Desconocido'
        };
    }
}
