FROM php:8.4-cli-alpine AS production

ARG UID=1000

ENV PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d:/usr/local/etc/php/conf-z.d

RUN set -xeu pipefail \
    && apk update \
    # Install PHP build deps
    && apk update && apk add --no-cache -t .deps $PHPIZE_DEPS \
    # Install PHP extension deps
    && apk add --no-cache \
        icu-dev \
        libpng-dev \
        libzip-dev \
        linux-headers \
        shadow \
        zlib-dev \
    && docker-php-ext-install \
        gd \
        intl \
        pdo_mysql \
        zip \
    && pecl install redis && docker-php-ext-enable redis \
    && usermod -u "${UID}" www-data \
    && groupmod -g "${UID}" www-data \
    && mkdir /app && chown www-data:www-data /app \
    && apk del .deps

WORKDIR /app

FROM production AS development

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN set -xeu pipefail \
    # Install dev deps  \
    && apk add --no-cache linux-headers bash git \
    # Install PHP build deps
    && apk update && apk add --no-cache -t .deps $PHPIZE_DEPS \
    # Install xdebug
    && pecl install xdebug && docker-php-ext-enable xdebug \
    # Install Symfony CLI
    && curl -sS https://get.symfony.com/cli/installer -o /tmp/symfony-installer.sh \
        && bash /tmp/symfony-installer.sh --install-dir=/usr/local/bin \
        && rm /tmp/symfony-installer.sh \
    && apk del .deps

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
