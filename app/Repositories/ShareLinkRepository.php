<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class ShareLinkRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(string $tipo, int $recursoId, int $creadoPor, array $permisos, ?string $fechaExpiracion, int $limiteDescargas = 0): string
    {
        $token = bin2hex(random_bytes(32));
        
        // Obtener el nombre del recurso
        $nombreRecurso = '';
        if ($tipo === 'archivo') {
            $stmt = $this->db->prepare('SELECT nombre FROM archivos WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $recursoId]);
            $row = $stmt->fetch();
            $nombreRecurso = $row ? $row['nombre'] : 'Archivo sin nombre';
        } else {
            $stmt = $this->db->prepare('SELECT nombre FROM carpetas WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $recursoId]);
            $row = $stmt->fetch();
            $nombreRecurso = $row ? $row['nombre'] : 'Carpeta sin nombre';
        }
        
        // Determinar el nivel de acceso basado en los permisos
        $nivelAcceso = 'ver';
        if (isset($permisos['write']) && $permisos['write']) {
            $nivelAcceso = 'editar';
        } elseif (isset($permisos['download']) && $permisos['download']) {
            $nivelAcceso = 'descargar';
        }
        
        // Determinar el rol de acceso
        $rolAcceso = 'lector';
        if (isset($permisos['write']) && $permisos['write']) {
            $rolAcceso = 'editor';
        }
        
        $stmt = $this->db->prepare('
            INSERT INTO enlaces_compartidos (
                token, 
                tipo, 
                recurso_tipo, 
                recurso_id, 
                creado_por, 
                propietario_id, 
                nombre_recurso, 
                nivel_acceso, 
                fecha_expiracion, 
                limite_accesos, 
                accesos_actuales, 
                activo,
                rol_acceso,
                puede_descargar,
                puede_imprimir,
                puede_copiar
            ) VALUES (
                :token,
                :tipo,
                :recurso_tipo,
                :rid,
                :uid,
                :propietario,
                :nombre,
                :nivel,
                :exp,
                :lim,
                0,
                1,
                :rol,
                :puede_descargar,
                :puede_imprimir,
                :puede_copiar
            )
        ');
        
        $stmt->execute([
            'token' => $token,
            'tipo' => $tipo,
            'recurso_tipo' => $tipo,
            'rid' => $recursoId,
            'uid' => $creadoPor,
            'propietario' => $creadoPor,
            'nombre' => $nombreRecurso,
            'nivel' => $nivelAcceso,
            'exp' => $fechaExpiracion,
            'lim' => $limiteDescargas,
            'rol' => $rolAcceso,
            'puede_descargar' => isset($permisos['download']) ? ($permisos['download'] ? 1 : 0) : 1,
            'puede_imprimir' => isset($permisos['print']) ? ($permisos['print'] ? 1 : 0) : 1,
            'puede_copiar' => isset($permisos['copy']) ? ($permisos['copy'] ? 1 : 0) : 1,
        ]);
        
        return $token;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM enlaces_compartidos WHERE token = :t AND activo = 1 LIMIT 1');
        $stmt->execute(['t' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function incrementAccess(string $token): void
    {
        $this->db->prepare('UPDATE enlaces_compartidos SET accesos_actuales = accesos_actuales + 1 WHERE token = :t')->execute(['t' => $token]);
    }
}


