<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use Core\Http\Request;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    /** @param array<string, mixed> $params */
    protected function get(string $action, string $controller, array $params = []): string|false
    {
        ob_start();
        $controllerInstance = new $controller();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_REQUEST = $params;

        $request = new Request();
        $controllerInstance->$action($request);

        $_REQUEST = [];
        return ob_get_clean();
    }

    protected function actingAs(User $user): void
    {
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'role' => $user->getRole()
        ];
    }

    protected function mockRedirectToRoute(object $controller): void
    {
    }
}
