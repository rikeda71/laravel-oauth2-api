FROM php:8.0.1-fpm-buster

ENV COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
  && apt-get -y install git unzip libzip-dev libicu-dev libonig-dev \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-install intl pdo_mysql zip bcmath \
  # && composer config -g process-timeout 3600 \
  # && composer config -g repos.packagist composer https://packagist.org \
  && pecl install xdebug-3.0.0 \
  && docker-php-ext-enable xdebug


WORKDIR /app
