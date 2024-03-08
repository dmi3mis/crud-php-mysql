FROM php:apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
COPY . /var/www/html

EXPOSE 80
