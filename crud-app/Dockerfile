FROM php:apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
RUN apt update && apt install -y bind9-dnsutils iputils-ping iproute2
COPY . /var/www/html

EXPOSE 80
