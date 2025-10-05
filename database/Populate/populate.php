<?php

require __DIR__ . '/../../config/bootstrap.php';

use Database\Populate\ProjectsPopulate;
use Database\Populate\UsersPopulate;


UsersPopulate::populate();
ProjectsPopulate::populate();