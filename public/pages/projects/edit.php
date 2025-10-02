<?php
$id = intval($_GET['id']);

define('DB_PATH', '/var/www/database/projects.txt');
$projects = file(DB_PATH, FILE_IGNORE_NEW_LINES);

$project['id'] = $id;
$project['title'] = $projects[$id];

$title = "Editar Projeto #{$id}";
$view = '/var/www/app/views/projects/edit.phtml';

require '/var/www/app/views/layouts/application.phtml';