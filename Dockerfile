FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

FROM php:8.3-apache

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

COPY --from=composer /app/vendor /var/www/html/vendor

RUN chown -R www-data:www-data /var/www/html

