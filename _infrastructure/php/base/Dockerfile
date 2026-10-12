# Определяем переменную PHP_MODE (по умолчанию 'cli')
ARG PHP_VERSION=8.4
ARG PHP_MODE=cli

# Выбираем базовый образ на основе MODE
FROM php:${PHP_VERSION}-${PHP_MODE}-alpine

# Общие шаги для всех режимов

# Утилиты для установки расширений и Composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Настройка пользователя (совместимость с хостом)
ARG PUID=1000
ARG PGID=1000
RUN apk add --no-cache shadow bash \
    && groupadd -g ${PGID} app \
    && useradd -u ${PUID} -g app -m app \
    && usermod -p "*" app -s /bin/bash

# Основные расширения PHP
COPY ./_infrastructure/_scripts/common-packages.sh /tmp/
RUN chmod +x /tmp/common-packages.sh && \
    /tmp/common-packages.sh

# Рабочая директория
WORKDIR /var/www/app

# Создаем необходимые директории и настраиваем права
RUN mkdir /var/www/app/vendor && \
    chmod -R 0775 /var/www/app && \
    chown -R app:app /var/www/app

# Переключаемся на пользователя app
USER app