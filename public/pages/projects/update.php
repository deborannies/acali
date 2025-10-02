<?php

$method = $_REQUEST['_method'] ?? $_SERVER['REQUEST_METHOD'];

if ($method !== 'PUT') {
    header('Location: /pages/projects');
    exit;
}

$project = $_POST['project'];

$id = $project['id'];
$title = trim($project['title']);

$errors = [];

if (empty($title))
    $errors['title'] = 'não pode ser vazio!';


if (empty($errors)) {
    define('DB_PATH', '/var/www/database/projects.txt');

    $projects = file(DB_PATH, FILE_IGNORE_NEW_LINES);
    $projects[$id] = $title;

    $data = implode(PHP_EOL, $projects);
    file_put_contents(DB_PATH, $data . PHP_EOL);

    header('Location: /pages/projects');
} else {
    $title = "Editar projecta #{$id}";
    $view = '/var/www/app/views/projects/edit.phtml';

    require '/var/www/app/views/layouts/application.phtml';
}