<?php

require __DIR__ . '/../../config/bootstrap.php';

use Database\Populate\ProjectsPopulate;
use Database\Populate\UsersPopulate;
use Database\Populate\ProjectUserPopulate;


UsersPopulate::populate();
ProjectsPopulate::populate();
ProjectUserPopulate::populate();