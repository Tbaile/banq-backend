#!/usr/bin/env sh

set -e

if [ -f .env ]; then
    . .env
fi

if [ "$1" = 'php-fpm' ]; then
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
