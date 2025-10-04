<?php

use Core\Router\Router;

if (!function_exists('dd')) {
    function dd(mixed ...$vars): void
    {
        dump(...$vars);
        die(1);
    }
}

if (!function_exists('route')) {
    /**
     *
     * @param string $name
     * @param array<string, mixed> $params
     * @return string
     */
    function route(string $name, array $params = []): string
    {
        return Router::getInstance()->getRoutePathByName($name, $params);
    }
}
