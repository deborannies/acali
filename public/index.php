<?php

// Inicia a sessão para o login funcionar
session_start();

// Carrega o autoloader e as configurações
require __DIR__ . '/../config/bootstrap.php';

// ESTA É A CORREÇÃO
// Em vez de chamar o método estaticamente, obtemos a instância e depois chamamos o dispatch.
// No entanto, o seu Router.php tem um método init() que já faz isto. Vamos usá-lo.
\Core\Router\Router::init();
