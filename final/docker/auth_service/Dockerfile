FROM php:7.4-fpm
WORKDIR /app
RUN apt-get update && apt-get install -y curl
RUN docker-php-ext-install pdo_mysql
COPY ./src /app
RUN curl -sS https://getcomposer.org/installer | \
            php -- --install-dir=/usr/bin/ --filename=composer
RUN /usr/bin/composer install
