#!/usr/bin/env sh

set -e

if [ "$1" = 'php-fpm' ]; then
    wait-for "${DB_HOST:?Missing DB_HOST}:${DB_PORT:?Missing DB_PORT}" -t 60
    if [ "$APP_ENV" = "local" ]; then
        composer i
    else
        php artisan config:cache
        php artisan view:cache
    fi
    php artisan migrate --force
    php artisan storage:link
    chown -R www-data:www-data storage
fi

exec "$@"
