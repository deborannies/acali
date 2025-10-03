<?php

require '/var/www/config/bootstrap.php';

use App\Controllers\ProjectsController;

$controller = new ProjectsController();
$controller->edit();
