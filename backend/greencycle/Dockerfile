# syntax=docker/dockerfile:1

FROM node:26-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


FROM dunglas/frankenphp:1-php8.5-bookworm AS application

WORKDIR /app

RUN setcap -r /usr/local/bin/frankenphp

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions \
        pdo_pgsql \
        mbstring \
        intl \
        zip \
        opcache \
        bcmath

COPY . .

COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

COPY Caddyfile /etc/caddy/Caddyfile
COPY scripts/render-start.sh /usr/local/bin/render-start

RUN chmod +x /usr/local/bin/render-start \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

EXPOSE 10000

CMD ["/usr/local/bin/render-start"]