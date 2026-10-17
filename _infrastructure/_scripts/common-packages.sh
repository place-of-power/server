#!/bin/sh

apk add --no-cache \
    unzip \
    git \
    libzip-dev \
    icu-dev

install-php-extensions \
    zip \
    intl \
    opcache \
    sockets \
    pcntl \
    exif \
    bcmath \
    pcov \
    pgsql \
    pdo_pgsql

# Рантайм-библиотеки держим, хедеры для сборки — чистим
apk add --no-cache \
    libzip \
    icu-libs

apk del --purge \
    libzip-dev \
    icu-dev

rm -rf /var/cache/apk/*