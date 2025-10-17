<?php

// Variabel global untuk menyimpan semua rute yang terdaftar
$routes = [];

/**
 * Mendaftarkan sebuah rute dengan method GET.
 *
 * @param string $uri URL yang akan dicocokkan.
 * @param array  $action File controller dan fungsi yang akan dijalankan.
 * @param string|null $middleware Nama middleware yang akan dijalankan sebelumnya.
 */
function route_get(string $uri, array $action, ?string $middleware = null)
{
    global $routes;
    $routes['GET'][$uri] = ['action' => $action, 'middleware' => $middleware];
}

/**
 * PENAMBAHAN: Mendaftarkan sebuah rute dengan method POST.
 *
 * @param string $uri URL yang akan dicocokkan.
 * @param array  $action File controller dan fungsi yang akan dijalankan.
 * @param string|null $middleware Nama middleware yang akan dijalankan sebelumnya.
 */
function route_post(string $uri, array $action, ?string $middleware = null)
{
    global $routes;
    $routes['POST'][$uri] = ['action' => $action, 'middleware' => $middleware];
}

/**
 * Mencari rute yang cocok dan menjalankan aksi yang sesuai.
 */
function dispatch()
{
    global $routes;

    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
    $uri = '/' . trim(str_replace($basePath, '', $requestUri), '/');
    $method = $_SERVER['REQUEST_METHOD'];

    if (isset($routes[$method][$uri])) {
        $route = $routes[$method][$uri];
        $action = $route['action'];
        $middleware = $route['middleware'];

        require_once __DIR__ . '/middleware.php';
        run_middleware($middleware);

        $controllerFile = __DIR__ . '/../app/' . $action[0];
        $functionName = $action[1];

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            if (function_exists($functionName)) {
                $functionName();
            } else {
                error_log("Fungsi '$functionName' tidak ada di file '$controllerFile'");
                abort_500();
            }
        } else {
            error_log("File controller '$controllerFile' tidak ditemukan untuk URI '$uri'");
            abort_500();
        }
    } else {
        abort_404();
    }
}

function abort_404()
{
    http_response_code(404);
    require_once __DIR__ . '/../app/templates/errors/404.php';
    exit();
}

function abort_500()
{
    http_response_code(500);
    require_once __DIR__ . '/../app/templates/errors/500.php';
    exit();
}
