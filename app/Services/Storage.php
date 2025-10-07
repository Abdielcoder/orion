<?php

namespace App\Services;

class Storage
{
    public static function basePath(): string
    {
        $path = dirname(__DIR__, 2) . '/storage/files';
        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }
        return $path;
    }

    public static function userPath(int $userId): string
    {
        $base = self::basePath();
        $path = $base . '/' . $userId;
        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }
        return $path;
    }
}


