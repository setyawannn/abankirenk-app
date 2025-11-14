<?php
// core/router.php

// Variabel global untuk menyimpan semua rute yang terdaftar
$routes = [];

/**
 * Mendaftarkan sebuah rute dengan method GET.
 * (Tidak ada perubahan di fungsi ini)
 */
function route_get(string $uri, array $action, ?string $middleware = null)
{
    global $routes;
    $routes['GET'][$uri] = ['action' => $action, 'middleware' => $middleware];
}

/**
 * Mendaftarkan sebuah rute dengan method POST.
 * (Tidak ada perubahan di fungsi ini)
 */
function route_post(string $uri, array $action, ?string $middleware = null)
{
    global $routes;
    $routes['POST'][$uri] = ['action' => $action, 'middleware' => $middleware];
}

/**
 * ====================================================================
 * FUNGSI DISPATCH YANG DIPERBARUI
 * ====================================================================
 * * Mencari rute yang cocok (termasuk parameter dinamis) 
 * dan menjalankan aksi yang sesuai.
 */
function dispatch()
{
    global $routes;

    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
    $uri = '/' . trim(str_replace($basePath, '', $requestUri), '/');
    $method = $_SERVER['REQUEST_METHOD'];

    if (!isset($routes[$method])) {
        abort_404();
        return;
    }

    $routeFound = false;
    $params = [];

    foreach ($routes[$method] as $routeUri => $routeDetails) {

        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routeUri, $paramNames);
        $paramNames = $paramNames[1];

        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $routeFound = true;
            array_shift($matches);

            if (count($paramNames) === count($matches)) {
                $params = array_combine($paramNames, $matches);
            }

            $action = $routeDetails['action'];
            $middleware = $routeDetails['middleware'];

            require_once __DIR__ . '/middleware.php';
            run_middleware($middleware);

            $controllerFile = __DIR__ . '/../app/' . $action[0];
            $functionName = $action[1];

            if (file_exists($controllerFile)) {
                require_once $controllerFile;

                if (function_exists($functionName)) {
                    $functionName($params);
                } else {
                    error_log("Fungsi '$functionName' tidak ada di file '$controllerFile'");
                    abort_500();
                }
            } else {
                error_log("File controller '$controllerFile' tidak ditemukan untuk URI '$uri'");
                abort_500();
            }

            break;
        }
    }

    if (!$routeFound) {
        abort_404();
    }
}

function abort_404()
{
    http_response_code(404);
    $viewPath = __DIR__ . '/../app/templates/errors/404.php';
    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        echo "404 Not Found";
    }
    exit();
}

function abort_500()
{
    http_response_code(500);
    $viewPath = __DIR__ . '/../app/templates/errors/500.php';
    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        echo "500 Internal Server Error";
    }
    exit();
}
