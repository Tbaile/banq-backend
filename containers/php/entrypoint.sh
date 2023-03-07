#!/usr/bin/env sh
set -e

ROLE=${ROLE:-app}

if [ $# -gt 0 ]; then
    exec "$@"
else
    if [ "$ROLE" = "app" ]; then
        exec php-fpm
    elif [ "$ROLE" = "setup" ]; then
        php artisan config:cache
        php artisan view:cache
        php artisan storage:link
        php artisan migrate --force
        chown -R www-data:www-data storage
    else
        echo "Unknown role '$ROLE'"
        exit 1
    fi
fi
