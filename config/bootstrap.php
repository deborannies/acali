<?php

require __DIR__ . '/../vendor/autoload.php';
dd('TESTE DE DEBUG FUNCIONANDO');

use Core\Env\EnvLoader;
use Core\Errors\ErrorsHandler;


ErrorsHandler::init();
EnvLoader::init();
