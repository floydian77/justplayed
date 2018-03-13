FROM php:7.2-fpm

ENV PHPREDIS_VERSION 3.0.0

RUN apt-get update && apt-get install -y libmcrypt-dev zlib1g-dev \
    --no-install-recommends \
    && docker-php-ext-install zip \
    && mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis
