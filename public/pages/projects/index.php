<?php

define('DB_PATH', '/var/www/database/projects.txt');

$projects = @file(DB_PATH, FILE_IGNORE_NEW_LINES);

if ($projects === false) {
    $projects = [];
}

$title = 'Projetos Cadastrados';
$view = '/var/www/app/views/projects/index.phtml';

require '/var/www/app/views/layouts/application.phtml';

