<?php
// app/Core/Router.php
namespace App\Core;

class Router {
    protected $routes = ['GET'=>[], 'POST'=>[]];

    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }
    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch($uri, $method) {
        $path = parse_url($uri, PHP_URL_PATH);
        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo "404 — страница не найдена";
            exit;
        }
        list($controller, $action) = explode('@', $this->routes[$method][$path]);
        $controller = "App\\Controllers\\{$controller}";
        (new $controller)->{$action}();
    }
}
