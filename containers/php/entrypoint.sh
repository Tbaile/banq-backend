#!/usr/bin/env sh

set -e

if [ -f .env ]; then
    . .env
fi

if [ "$1" = 'php-fpm' ]; then
    wait-for "${DB_HOST}:${DB_PORT}" --timeout=60
    php artisan migrate --force
    if [ "$APP_ENV" = "local" ]; then
        composer i
    else
        php artisan optimize
    fi
    php artisan storage:link
elif [ "$1" = 'worker' ]; then
    wait-for "${DB_HOST}:${DB_PORT}" --timeout=60
    set -- php artisan queue:work --tries=3
fi


exec "$@"
