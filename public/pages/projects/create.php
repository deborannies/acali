<?php

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    header('Location: /pages/projects');
    exit;
}

$project = $_POST['project'];
$title = trim($project['title']);

$errors = [];

if (empty($title))
    $errors['title'] = 'não pode ser vazio!';


if (empty($errors)) {
    define('DB_PATH', '/var/www/database/projects.txt');
    file_put_contents(DB_PATH, $title . PHP_EOL, FILE_APPEND);

    header('Location: /pages/projects');
} else {
    $title = 'Novo Projeto';
    $view = '/var/www/app/views/projects/new.phtml';

    require '/var/www/app/views/layouts/application.phtml';
}