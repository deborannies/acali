<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    public function get(string $action, string $controller): string
    {
        $controllerInstance = new $controller();
        ob_start();
        $controllerInstance->$action();
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }
}
