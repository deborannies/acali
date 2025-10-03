<?php

$envs = parse_ini_file(ROOT_PATH . '/.env');

foreach ($envs as $key => $value) {

    if (!isset($_ENV[$key])) {
        $_ENV[$key] = $value;
    }
}