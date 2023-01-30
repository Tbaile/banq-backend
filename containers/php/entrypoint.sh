#!/usr/bin/env sh
set -e

ROLE=${ROLE:-app}

if [ -n "$1" ]; then
    exec "$@"
else
    if [ "$ROLE" = "app" ]; then
        exec php-fpm
    elif [ "$ROLE" = "setup" ]; then
        php artisan app:setup
    else
        echo "Unknown role '$ROLE'"
        exit 1
    fi
fi
