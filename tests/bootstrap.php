<?php

use Core\Database\Database;
use Database\Populate\ProjectsPopulate;
use Database\Populate\UsersPopulate;

require __DIR__ . '/../config/bootstrap.php';

echo "A preparar a base de dados de teste...\n";

$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_DATABASE');
$dbUser = getenv('DB_USERNAME');
$dbPass = getenv('DB_PASSWORD');

try {
    $pdo_admin = Database::getConn();

    $pdo_admin->exec("DROP DATABASE IF EXISTS `{$dbName}`");
    $pdo_admin->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Base de dados '{$dbName}' criada.\n";

    $pdo_test = new PDO(
        "mysql:host={$dbHost};dbname={$dbName}",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $schemaSql = file_get_contents(__DIR__ . '/../database/schema.sql');
    $pdo_test->exec($schemaSql);

    echo "Schema.sql executado.\n";

    Database::setTestConnection($pdo_test);

    UsersPopulate::populate();
    ProjectsPopulate::populate();

    echo "Populate executado. A iniciar testes...\n\n";
} catch (PDOException $e) {
    die("Falha ao configurar a base de dados de teste: " . $e->getMessage());
}
