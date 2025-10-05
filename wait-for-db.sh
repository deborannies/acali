#!/bin/bash
# Espera até o MySQL estar pronto
echo "Aguardando MySQL ficar disponível..."
until mysqladmin ping -h db -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  sleep 2
done

echo "MySQL pronto! Inicializando banco..."
php /var/www/init-database.php

echo "Iniciando PHP-FPM..."
php-fpm
