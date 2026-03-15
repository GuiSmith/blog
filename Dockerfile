FROM php:8.3.6

# instalar extensão necessária para o Eloquent
RUN docker-php-ext-install pdo pdo_mysql

RUN printf "error_reporting = E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED\n\
display_errors = On\n\
display_startup_errors = On\n" > /usr/local/etc/php/conf.d/dev.ini

WORKDIR /app

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]