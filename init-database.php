<?php

require __DIR__ . '/config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\UsersPopulate;
use Database\Populate\ProjectsPopulate;

echo "🚀 Iniciando configuração do banco de dados...\n";

Database::create();
Database::migrate();

UsersPopulate::populate();
ProjectsPopulate::populate();

echo "🎉 Banco de dados pronto para uso!\n";
