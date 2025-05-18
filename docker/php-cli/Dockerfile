FROM php:8.4-cli-alpine

ARG UID=1000

ENV PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d:/usr/local/etc/php/conf-z.d

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN set -xeu pipefail; \
    # Install dev deps  \
    apk add --no-cache --update linux-headers bash git \
    # Install PHP deps
    && apk update && apk add --no-cache -t .deps $PHPIZE_DEPS \
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
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && curl -sS https://get.symfony.com/cli/installer -o /tmp/symfony-installer.sh \
        && bash /tmp/symfony-installer.sh --install-dir=/usr/local/bin \
        && rm /tmp/symfony-installer.sh \
    && usermod -u "${UID}" www-data \
    && groupmod -g "${UID}" www-data \
    && mkdir /app && chown www-data:www-data /app

WORKDIR /app

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
