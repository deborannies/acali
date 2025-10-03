<?php

namespace Tests;

use PHPUnit\Framework\TestCase as FrameworkTestCase;

// Carrega as constantes e funções necessárias para os testes
require_once dirname(__DIR__) . '/core/constants/general.php';
require_once ROOT_PATH . '/core/env/env.php';

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
        // Usa as novas constantes para encontrar o arquivo de BD de teste
        $file = DATABASE_PATH . $_ENV['DB_NAME'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
}