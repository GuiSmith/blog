FROM php:8.3.6

# instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev

# instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# instalar composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# config php para desenvolvimento
RUN printf "error_reporting = E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED\n\
display_errors = On\n\
display_startup_errors = On\n" > /usr/local/etc/php/conf.d/dev.ini

WORKDIR /app

# copiar composer primeiro (cache do docker)
COPY composer.json composer.lock* ./

# instalar dependências
RUN composer install

# copiar resto do projeto
COPY . .

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
