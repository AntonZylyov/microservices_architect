FROM php:7.4-fpm
WORKDIR /app
RUN apt-get update && apt-get install -y curl && apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install curl
COPY ./src /app
RUN curl -sS https://getcomposer.org/installer | \
            php -- --install-dir=/usr/bin/ --filename=composer
RUN /usr/bin/composer install
