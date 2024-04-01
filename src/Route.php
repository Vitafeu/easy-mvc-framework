<?php 

namespace Vitafeu\EasyMVC;

class Route {
    protected $controller;
    protected $action;
    protected $middlewares = [];

    public function __construct($controller, $action) {
        $this->controller = $controller;
        $this->action = $action;
    }

    public function middleware($middleware) {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function getMiddlewares() {
        return $this->middlewares;
    }
}