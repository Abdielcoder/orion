<?php

namespace App\Helpers;

class Response
{
    public static function json(array $data, int $statusCode = 200, array $headers = []): void
    {
        // Clean any output buffer to prevent HTML before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // End output buffering
        if (ob_get_level()) {
            ob_end_flush();
        }
    }

    public static function view(string $view, array $params = []): void
    {
        extract($params, EXTR_SKIP);
        $baseViewPath = dirname(__DIR__, 2) . '/app/Views/';
        require $baseViewPath . $view . '.php';
    }
}


