<?php

namespace Tests\Unit\Controllers;

use Core\Http\Request;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    protected function get(string $action, string $controller): string|false
    {
        ob_start();
        $controllerInstance = new $controller();
        $request = new Request();
        $controllerInstance->$action($request);
        return ob_get_clean();
    }
}
