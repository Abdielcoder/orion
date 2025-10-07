<?php

namespace App\Helpers;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function add(string $method, string $path, callable|array $handler, array $middlewares = []): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $handler;
        if (!empty($middlewares)) {
            $this->middlewares[$method][$path] = $middlewares;
        }
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $path = '/' . ltrim(str_replace($base, '', $uri), '/');
        // Normalizaciones comunes
        if ($path === '//' || $path === '') { $path = '/'; }
        if ($path === '/index.php') { $path = '/'; }
        if (function_exists('str_starts_with') && str_starts_with($path, '/index.php/')) {
            $path = '/' . ltrim(substr($path, strlen('/index.php/')), '/');
        } elseif (strpos($path, '/index.php/') === 0) { // fallback PHP
            $path = '/' . ltrim(substr($path, strlen('/index.php/')), '/');
        }

        // Buscar coincidencia exacta primero
        $handler = $this->routes[$method][$path] ?? null;
        $matchedRoute = $path;
        $params = [];
        
        // Si no hay coincidencia exacta, buscar rutas con parámetros
        if (!$handler && isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $routeHandler) {
                if (strpos($route, '{') !== false) {
                    $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
                    $pattern = '#^' . $pattern . '$#';
                    
                    if (preg_match($pattern, $path, $matches)) {
                        $handler = $routeHandler;
                        $matchedRoute = $route;
                        
                        // Extraer parámetros
                        preg_match_all('/\{([^}]+)\}/', $route, $paramNames);
                        for ($i = 1; $i < count($matches); $i++) {
                            $paramName = $paramNames[1][$i - 1];
                            $params[$paramName] = $matches[$i];
                        }
                        break;
                    }
                }
            }
        }
        
        if (!$handler) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $middlewares = $this->middlewares[$method][$matchedRoute] ?? [];
        $next = function () use ($handler, $params) {
            if (is_array($handler)) {
                [$class, $action] = $handler;
                $controller = new $class();
                
                // Pasar parámetros como argumentos del método
                if (!empty($params)) {
                    $reflection = new \ReflectionMethod($controller, $action);
                    $methodParams = [];
                    foreach ($reflection->getParameters() as $param) {
                        $paramName = $param->getName();
                        if (isset($params[$paramName])) {
                            $methodParams[] = $params[$paramName];
                        }
                    }
                    return call_user_func_array([$controller, $action], $methodParams);
                } else {
                    return $controller->$action();
                }
            }
            return call_user_func($handler);
        };

        foreach (array_reverse($middlewares) as $middleware) {
            $next = function () use ($middleware, $next) {
                return (new $middleware())->handle($next);
            };
        }

        $next();
    }
}


