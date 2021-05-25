FROM php:7.4-fpm
WORKDIR /app
RUN apt-get update && apt-get install -y curl && apt install -y zip unzip php-zip
RUN docker-php-ext-install pdo_mysql
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis
COPY ./src /app
RUN curl -sS https://getcomposer.org/installer | \
            php -- --install-dir=/usr/bin/ --filename=composer
RUN /usr/bin/composer install
