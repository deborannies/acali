<?php
$id = intval($_GET['id']);

define('DB_PATH', '/var/www/database/projects.txt');
$projects = file(DB_PATH, FILE_IGNORE_NEW_LINES);

$project['title'] = $projects[$id];

$title = "Visualização do Projeto #{$id}";
$view = '/var/www/app/views/projects/show.phtml';

require '/var/www/app/views/layouts/application.phtml';