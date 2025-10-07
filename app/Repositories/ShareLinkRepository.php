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
        $stmt = $this->db->prepare('INSERT INTO enlaces_compartidos (token, tipo, recurso_id, creado_por, permisos, fecha_expiracion, limite_descargas, contador_accesos, activo) VALUES (:token,:tipo,:rid,:uid,:permisos,:exp,:lim,0,1)');
        $stmt->execute([
            'token' => $token,
            'tipo' => $tipo,
            'rid' => $recursoId,
            'uid' => $creadoPor,
            'permisos' => json_encode($permisos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'exp' => $fechaExpiracion,
            'lim' => $limiteDescargas,
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
        $this->db->prepare('UPDATE enlaces_compartidos SET contador_accesos = contador_accesos + 1 WHERE token = :t')->execute(['t' => $token]);
    }
}


