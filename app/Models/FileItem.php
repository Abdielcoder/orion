<?php

namespace App\Models;

class FileItem
{
    public int $id = 0;
    public string $nombre = '';
    public ?string $nombre_original = null;
    public ?string $tipo_mime = null;
    public ?string $extension = null;
    public ?int $tamaño = null;
    public ?int $carpeta_id = null;
    public int $propietario_id = 0;
    public int $version = 1;
    public string $ruta_local = '';
    public int $activo = 1;
}


