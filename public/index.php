<?php

use Core\Router\Router;

// 1. Carrega o autoloader e as configurações iniciais
require __DIR__ . '/../config/bootstrap.php';

// 2. Pega a instância do Roteador
$router = Router::getInstance();

// 3. Carrega TODAS as rotas definidas no seu arquivo
require __DIR__ . '/../config/routes.php';

// 4. Agora, com as rotas carregadas, despacha a requisição
$router->dispatch();
