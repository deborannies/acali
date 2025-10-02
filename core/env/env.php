<?php

// Este script lê o arquivo .env na raiz do projeto...
$envs = parse_ini_file('/var/www/.env');

// ...e carrega cada variável na superglobal $_ENV.
foreach ($envs as $key => $value) {
    $_ENV[$key] = $value;
}
