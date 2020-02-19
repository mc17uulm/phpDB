FROM php:7.4.2-apache
RUN a2enmod rewrite
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql