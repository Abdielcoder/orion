<?php

namespace App\Middlewares;

use App\Services\Session;

class RateLimitMiddleware
{
    private int $limit;
    private int $windowSeconds;

    public function __construct(int $limit = 30, int $windowSeconds = 60)
    {
        $this->limit = $limit;
        $this->windowSeconds = $windowSeconds;
    }

    public function handle(callable $next)
    {
        $key = 'rate_' . ($_SERVER['REQUEST_URI'] ?? '/') . '_' . ($_SERVER['REMOTE_ADDR'] ?? '');
        $bucket = Session::get($key, ['count' => 0, 'reset' => time() + $this->windowSeconds]);
        if (time() > $bucket['reset']) {
            $bucket = ['count' => 0, 'reset' => time() + $this->windowSeconds];
        }
        $bucket['count']++;
        Session::set($key, $bucket);
        if ($bucket['count'] > $this->limit) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Demasiadas solicitudes. Intente más tarde.']);
            return;
        }
        return $next();
    }
}


