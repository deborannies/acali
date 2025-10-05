<?php

namespace Core\Router;

use Core\Constants\Constants;
use Core\Http\Request;
use Core\Exceptions\HTTPException;

class Router
{
    private static Router|null $instance = null;
    /** @var Route[] $routes */
    private array $routes = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    public function addRoute(Route $route): Route
    {
        $this->routes[] = $route;
        return $route;
    }

    /**
     * @param string $name
     * @param array<string, mixed> $params
     * @return string
     */
    public function getRoutePathByName(string $name, array $params = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                $routePath = $route->getUri();
                $routePath = $this->replaceRouteParams($routePath, $params);
                $routePath = $this->appendQueryParams($routePath, $params);
                return $routePath;
            }
        }

        throw new \Exception("Route with name {$name} not found", 500);
    }

    /**
     * @param array<string, mixed> &$params
     */
    private function replaceRouteParams(string $routePath, array &$params): string
    {
        foreach ($params as $param => $value) {
            $routeParam = "{{$param}}";
            if (str_contains($routePath, $routeParam)) {
                $routePath = str_replace($routeParam, (string)$value, $routePath);
                unset($params[$param]);
            }
        }
        return $routePath;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function appendQueryParams(string $routePath, array $params): string
    {
        if (!empty($params)) {
            $routePath .= '?' . http_build_query($params);
        }
        return $routePath;
    }

    public function dispatch(): object|bool
    {
        $request = new Request();

        foreach ($this->routes as $route) {
            if ($route->match($request)) {
                $class = $route->getControllerName();
                // A CORREÇÃO DO ERRO DE DIGITAÇÃO ESTÁ AQUI
                $action = $route->getActionName();

                $controller = new $class();
                $controller->$action($request);

                return $controller;
            }
        }

        return throw new HTTPException('URI ' . $request->getUri() . ' not found.', 404);
    }

    public static function init(): void
    {
        if (!empty($_REQUEST)) {
            require Constants::rootPath()->join('config/routes.php');
            Router::getInstance()->dispatch();
        }
    }
}