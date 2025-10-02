<?php

define('PROJECT_ROOT', dirname(__DIR__, 3));
define('DB_PATH', PROJECT_ROOT . '/database/projects.txt');

if (!isset($_GET['id'])) {
    die('ID do projeto não fornecido.');
}
$projectId = $_GET['id'];

$projects = file(DB_PATH, FILE_IGNORE_NEW_LINES);

if (isset($projects[$projectId])) {
    unset($projects[$projectId]);
    file_put_contents(DB_PATH, implode(PHP_EOL, $projects));
}

header('Location: /');
exit;

