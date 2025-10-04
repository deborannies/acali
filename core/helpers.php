<?php

use Core\Debug\Debugger;
use Core\Router\Router;

if (!function_exists('dd')) {
    function dd(mixed ...$vars): void
    {
        dump(...$vars);

        die(1);
    }
}

if (!function_exists('route')) {
    function route(string $name): string
    {
        return Router::getInstance()->getRoutePathByName($name);
    }
}
