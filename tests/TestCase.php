<?php

namespace Tests;

use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase
{
    public function setUp(): void
    {
        $this->clearDatabase();
    }

    public function tearDown(): void
    {
        $this->clearDatabase();
    }

    private function clearDatabase()
    {
        $file = '/var/www/database/' . $_ENV['DB_NAME'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
}