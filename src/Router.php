<?php

namespace Vitafeu\EasyMVC;

use Vitafeu\EasyMVC\Globals;
use Vitafeu\EasyMVC\Route;

class Router {
    protected $controllersNamespace = '';
    protected $middlewaresNamespace = '';
    protected $routesPath;
    protected $routes = [];

    public function __construct() {
        $this->routesPath = Globals::getProjectRoot() . 'routes/web.php';
    }

    public function start() {
        $this->loadRoutes();
        $this->handleRequest();
    }

    public function loadRoutes() {
        if (file_exists($this->routesPath)) {
            include $this->routesPath;
        } else {
            throw new \Exception("Routes file not found: " . $this->routesPath);
        }
    }

    public function addRoute($method, $path, $controller, $action) {
        $route = new Route($this->controllersNamespace . $controller, $action);
        $this->routes[$method][$path] = $route;

        return $route;
    }

    public function dispatch($method, $uri) {
        // GET
        $urlParts = explode('?', $uri);
        $uri = $urlParts[0];
        $queryParams = isset($urlParts[1]) ? $urlParts[1] : '';

        if (array_key_exists($method, $this->routes) && array_key_exists($uri, $this->routes[$method])) {
            $controller = $this->routes[$method][$uri]->getController();
            $action = $this->routes[$method][$uri]->getAction();

            // GET Parameters
            $params = [];
            parse_str($queryParams, $params);

            // POST Parameters
            if ($method === 'POST') {
                $params = array_merge($_POST, $params);
            }

            // Middlewares
            $middlewares = $this->routes[$method][$uri]->getMiddlewares();
            foreach ($middlewares as $middleware) {
                $midd = $this->middlewaresNamespace . $middleware;

                if (!class_exists($midd)) {
                    throw new \Exception("Middleware not found: $midd");
                }

                $middlewareInstance = new $midd();
                $middlewareInstance($params);
            }

            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action($params);
            } else {
                throw new \Exception("Action not found for URI: $uri");
            }
        } else {
            throw new \Exception("No route found for URI: $uri");
        }
    }

    public function handleRequest() {
        try {
            $this->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function setControllersNamespace($namespace) {
        $this->controllersNamespace = $namespace;
    }

    public function setMiddlewaresNamespace($namespace) {
        $this->middlewaresNamespace = $namespace;
    }
}