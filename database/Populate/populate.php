<?php

require __DIR__ . '/../../config/bootstrap.php';

// Remova as classes não utilizadas se quiser
use Database\Populate\ProjectsPopulate;
use Database\Populate\UsersPopulate;

// O script de populate deve apenas popular os dados.
// As linhas Database::create() e Database::migrate() foram removidas.

UsersPopulate::populate();
ProjectsPopulate::populate();