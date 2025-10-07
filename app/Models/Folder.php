<?php

namespace App\Models;

class Folder
{
    public int $id;
    public string $nombre;
    public ?int $padre_id = null;
    public int $propietario_id;
    public int $activa = 1;
    public ?string $etiqueta = null;
    public ?string $color_etiqueta = null;
    public ?string $icono_personalizado = null;
}


