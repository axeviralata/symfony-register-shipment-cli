FROM php:8.2.1-fpm
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update && apt-get --assume-yes  install git

WORKDIR /var/www