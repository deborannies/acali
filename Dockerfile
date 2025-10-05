FROM php:8.3.4-fpm

# Adiciona as dependências do sistema (unzip) e as extensões do PHP (zip, pdo_mysql)
# O 'zip' e 'unzip' são necessários para o Composer funcionar corretamente.
# O 'pdo_mysql' é para a sua aplicação se conectar ao banco de dados.
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Copia o executável do Composer para dentro da imagem
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Esta linha habilita a extensão, mantida do seu arquivo original.
RUN docker-php-ext-enable pdo_mysql