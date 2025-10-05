<?php

namespace App\Controllers;

class BaseController
{
    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = [], string $layout = 'application'): void
    {
        $viewPath = '/var/www/app/views/' . $view . '.phtml';
        extract($data);
        require '/var/www/app/views/layouts/' . $layout . '.phtml';
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function redirectToRoute(string $routeName, array $params = []): void
    {
        $location = route($routeName, $params);
        header('Location: ' . $location);
        exit;
    }
}