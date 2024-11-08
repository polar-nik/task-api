FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    curl \
    git \
    && docker-php-ext-install mysqli pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www