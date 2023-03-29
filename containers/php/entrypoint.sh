#!/usr/bin/env sh

set -e

if [ "$1" = 'setup' ]; then
    wait-for "${DB_HOST:?Missing DB_HOST}:${DB_PORT:?Missing DB_PORT}" -t 60
    php artisan config:cache
    php artisan view:cache
    php artisan storage:link
    php artisan migrate --force
    chown -R www-data:www-data storage
else
    exec "$@"
fi
