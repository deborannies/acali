<?php

namespace Tests\Unit\Core\Router;

use Core\Constants\Constants;
use Core\Http\Request;
use Core\Router\Route;
use Core\Router\Router;
use Tests\TestCase;

class RouteTest extends TestCase
{
    public function test_should_create_route_using_constructor(): void
    {
        $route = new Route('GET', '/', controllerName: MockController::class, actionName: 'action');

        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals('/', $route->getUri());
        $this->assertEquals(MockController::class, $route->getControllerName());
        $this->assertEquals('action', $route->getActionName());
    }

    public function test_should_add_route_to_the_router_method_get(): void
    {
        $this->assertRouteWasAddedToRouter('GET', 'get');
    }

    public function test_should_add_route_to_the_router_method_post(): void
    {
        $this->assertRouteWasAddedToRouter('POST', 'post');
    }

    public function test_should_add_route_to_the_router_method_put(): void
    {
        $this->assertRouteWasAddedToRouter('PUT', 'put');
    }

    public function test_should_add_route_to_the_router_method_delete(): void
    {
        $this->assertRouteWasAddedToRouter('DELETE', 'delete');
    }

    public function test_name_should_set_the_name_of_the_route(): void
    {
        $route = new Route('GET', '/', controllerName: MockController::class, actionName: 'index');
        $route->name('root');
        $this->assertEquals('root', $route->getName());
    }

    public function test_match_should_return_true_if_method_and_uri_with_params_match(): void
    {
        $route = new Route('GET', '/test/{id}', controllerName: MockController::class, actionName: 'show');
        $route->name('test.show');

        $this->assertTrue($route->match($this->request('GET', '/test/1')));
        $this->assertFalse($route->match($this->request('POST', '/test/1')));
        $this->assertFalse($route->match($this->request('GET', '/test/1/edit')));
        $this->assertFalse($route->match($this->request('GET', '/test')));
    }

    public function test_match_should_return_true_and_add_params_if_method_and_uri_with_params_match(): void
    {
        $route = new Route('GET', '/test/{id}', controllerName: MockController::class, actionName: 'show');
        $request = $this->request('GET', '/test/1');

        $this->assertTrue($route->match($request));
        $this->assertEquals(['id' => '1'], $request->getParams());
    }

    private function assertRouteWasAddedToRouter(string $httpMethod, string $staticMethodName): void
    {
        // Use reflection to manipulate the singleton instance for testing
        $routerReflection = new \ReflectionClass(Router::class);
        $instanceProperty = $routerReflection->getProperty('instance');
        $instanceProperty->setAccessible(true);

        // Store the original instance
        $originalInstance = $instanceProperty->getValue();

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects($this->once())
            ->method('addRoute')
            ->with($this->callback(function ($route) use ($httpMethod) {
                return $route instanceof Route
                    && $route->getMethod() === $httpMethod
                    && $route->getUri() === '/test'
                    && $route->getControllerName() === 'TestController'
                    && $route->getActionName() === 'test';
            }));

        // Set the mocked instance
        $instanceProperty->setValue(null, $routerMock);

        $route = Route::$staticMethodName('/test', ['TestController', 'test']);
        $this->assertInstanceOf(Route::class, $route);

        // Restore the original instance
        $instanceProperty->setValue(null, $originalInstance);
    }

    private function request(string $method, string $uri): Request
    {
        require_once Constants::rootPath()->join('tests/Unit/Core/Http/header_mock.php');
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_REQUEST = [];
        return new Request();
    }
}
