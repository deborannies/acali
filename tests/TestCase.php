<?php

namespace Tests;

use Core\Constants\Constants;
use Core\Database\Database;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase
{
    public function setUp(): void
    {
        require Constants::rootPath()->join('config/routes.php');
        Database::create();
        Database::migrate();
    }

    public function tearDown(): void
    {
        Database::drop();

        $routerReflection = new \ReflectionClass(\Core\Router\Router::class);
        $instanceProperty = $routerReflection->getProperty('instance');
        $instanceProperty->setValue(null, null);

        $_SESSION = [];
    }

    protected function getOutput(callable $callable): string
    {
        ob_start();
        $callable();
        return ob_get_clean();
    }

    protected function mockAdminUser(): \App\Models\User
    {
        $admin = new \App\Models\User([
            'name' => 'Admin Test User',
            'email' => 'admin@test.com',
            'password' => '123456',
            'role' => 'admin'
        ]);
        $admin->save();

        $_SESSION['user'] = [
            'id' => $admin->getId(),
            'role' => $admin->getRole()
        ];

        return $admin;
    }

    protected function mockRegularUser(): \App\Models\User
    {
        $user = new \App\Models\User([
            'name' => 'Regular Test User',
            'email' => 'user@test.com',
            'password' => '123456',
            'role' => 'user'
        ]);
        $user->save();

        $_SESSION['user'] = [
            'id' => $user->getId(),
            'role' => $user->getRole()
        ];

        return $user;
    }
}
