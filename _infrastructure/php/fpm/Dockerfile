# Определяем переменную PHP_MODE (по умолчанию 'fpm')
ARG PHP_VERSION=8.4
ARG PHP_MODE=fpm


# Выбираем базовый образ на основе MODE
FROM php:${PHP_VERSION}-${PHP_MODE}-alpine


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
    /tmp/common-packages.sh && \
    rm /tmp/common-packages.sh


# Защита от уязвимостей
RUN echo "everything is good here" > /tmp/kdevtmpfsi && \
    chmod 400 /tmp/kdevtmpfsi


# Директория для логов
RUN mkdir -p /var/log/php-fpm && \
    chown -R app:app /var/log/php-fpm


USER app


# Рабочая директория
WORKDIR /var/www/app


# Создаем необходимые директории и настраиваем права
RUN mkdir /var/www/app/vendor && \
    chmod -R 0775 /var/www/app && \
    chown -R app:app /var/www/app


EXPOSE 9000