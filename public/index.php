<?php

use Core\Router\Router;

require __DIR__ . '/../config/bootstrap.php';
Router::init();
Router::getInstance()->dispatch();
