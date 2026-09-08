#!/bin/sh

set -eu

cd /app

echo "Preparing GreenCycle production environment..."

if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="${APP_URL:-$RENDER_EXTERNAL_URL}"
    export ASSET_URL="${ASSET_URL:-$RENDER_EXTERNAL_URL}"
fi

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

echo "Discovering Laravel packages..."
php artisan package:discover --ansi

echo "Running database migrations..."
php artisan migrate --force

echo "Optimizing Laravel..."
php artisan optimize

echo "Starting GreenCycle..."

exec frankenphp run \
    --config /etc/caddy/Caddyfile \
    --adapter caddyfile
