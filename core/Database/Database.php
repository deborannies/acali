<?php

namespace Core\Database;

use Core\Constants\Constants;
use PDO;

class Database
{
    /**
     * @var \PDO|null A nossa ligação Singleton (estática).
     */
    private static ?\PDO $conn = null;

    public static function getDatabaseConn(): \PDO
    {
        if (self::$conn !== null) {
            return self::$conn;
        }

        $user = $_ENV['DB_USERNAME'];
        $pwd = $_ENV['DB_PASSWORD'];
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db = $_ENV['DB_DATABASE'];

        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db;

        self::$conn = new PDO($dsn, $user, $pwd);
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return self::$conn;
    }

    public static function getConn(): PDO
    {
        $user = $_ENV['DB_USERNAME'];
        $pwd = $_ENV['DB_PASSWORD'];
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];

        $dsn = 'mysql:host=' . $host . ';port=' . $port;

        $pdo = new PDO($dsn, $user, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function setTestConnection(PDO $pdo): void
    {
        self::$conn = $pdo;
    }


    public static function create(): void
    {
        $sql = 'CREATE DATABASE IF NOT EXISTS `' . $_ENV['DB_DATABASE'] . '`;';
        self::getConn()->exec($sql);
    }

    public static function drop(): void
    {
        $sql = 'DROP DATABASE IF EXISTS `' . $_ENV['DB_DATABASE'] . '`;';
        self::getConn()->exec($sql);
    }

    public static function migrate(): void
    {
        $sql = file_get_contents(Constants::databasePath()->join('schema.sql'));
        self::getDatabaseConn()->exec($sql);
    }
}