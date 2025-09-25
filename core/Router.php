<?php

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get($uri, $action)
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action)
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function handleRequest()
    {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = ($basePath === '/' || $basePath === '\\') ? '' : $basePath;

        $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
        $uri = substr($requestUri, strlen($basePath));
        $uri = trim($_GET['url'] ?? '', '/');

        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];
            $this->callAction($action);
        } else {
            // This will now be called correctly
            $this->abort(404);
        }
    }

    protected function callAction($action)
    {
        list($controllerName, $methodName) = explode('@', $action);

        $controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";


        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();

            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                // This is a programmer error. The method doesn't exist.
                // It should be a 500 error, not a plain echo.
                $this->abort(500); 
            }
        } else {
            // This is also a programmer error. The controller file is missing.
            // It should be a 500 error.
            $this->abort(500);
        }
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        $error_code = $code;

        require_once __DIR__ . '/../app/views/error/error.php';

        die();
    }
}
